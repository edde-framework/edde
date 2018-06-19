<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Filter\FilterException;
	use Edde\Schema\ISchema;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Validator\ValidatorManager;
	use Edde\Validator\ValidatorException;
	use Throwable;

	class InsertQuery extends AbstractQuery {
		use Storage;
		use SchemaManager;
		use FilterManager;
		use ValidatorManager;
		/** @var string */
		protected $prefix;

		/**
		 * @param string $prefix
		 */
		public function __construct(string $prefix = 'storage') {
			$this->prefix = $prefix;
		}

		/**
		 * @param string $name
		 * @param array  $insert
		 *
		 * @return array
		 *
		 * @throws Throwable
		 */
		public function insert(string $name, array $insert): array {
			try {
				$schema = $this->schemaManager->getSchema($name);
				$columns = [];
				$params = [];
				foreach ($source = $this->input($schema, $insert) as $k => $v) {
					$columns[sha1($k)] = $this->storage->delimit($k);
					$params[sha1($k)] = $v;
				}
				if (empty($params)) {
					return [];
				}
				$this->storage->exec(
					vsprintf('INSERT INTO %s (%s) VALUES (:%s)', [
						$this->storage->delimit($schema->getRealName()),
						implode(',', $columns),
						implode(',:', array_keys($params)),
					]),
					$params
				);
				return $this->storageFilterService->output($schema, $source);
			} catch (Throwable $exception) {
				/** @noinspection PhpUnhandledExceptionInspection */
				throw $this->storage->exception($exception);
			}
		}

		/**
		 * @param ISchema $schema
		 * @param array   $input
		 *
		 * @return array
		 *
		 * @throws FilterException
		 * @throws ValidatorException
		 */
		public function input(ISchema $schema, array $input): array {
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (($generator = $attribute->getFilter('generator')) && $input[$name] === null) {
					$input[$name] = $this->filterManager->getFilter($this->prefix . ':' . $generator)->input(null);
				}
				$input[$name] = $input[$name] ?: $attribute->getDefault();
				if ($validator = $attribute->getValidator()) {
					$this->validatorManager->validate($this->prefix . ':' . $validator, $input[$name], (object)[
						'name'     => $schema->getName() . '::' . $name,
						'required' => $attribute->isRequired(),
					]);
				}
				$input[$name] = $this->value($attribute, $input[$name]);
			}
			return $input;
		}
	}
