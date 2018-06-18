<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Edde;
	use Edde\Filter\FilterException;
	use Edde\Schema\IAttribute;
	use Edde\Schema\ISchema;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Validator\ValidatorManager;
	use function array_key_exists;
	use function array_map;
	use function is_array;
	use function iterator_to_array;

	class StorageFilterService extends Edde implements IStorageFilterService {
		use FilterManager;
		use ValidatorManager;
		use SchemaManager;
		/** @var string */
		protected $prefix;

		/**
		 * @param string $prefix
		 */
		public function __construct(string $prefix = 'storage') {
			$this->prefix = $prefix;
		}

		/** @inheritdoc */
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

		/** @inheritdoc */
		public function update(ISchema $schema, array $update): array {
			foreach ($schema->getAttributes() as $name => $attribute) {
				if ($validator = $attribute->getValidator()) {
					$this->validatorManager->validate($this->prefix . ':' . $validator, $update[$name], (object)[
						'name'     => $schema->getName() . '::' . $name,
						'required' => $attribute->isRequired(),
					]);
				}
				if ($filter = $attribute->getFilter('type')) {
					$update[$name] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($update[$name]);
				}
				/**
				 * common filter support; filter name is used for both directions
				 */
				if ($filter = $attribute->getFilter('filter')) {
					$update[$name] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($update[$name]);
				}
			}
			return $update;
		}

		/** @inheritdoc */
		public function output(ISchema $schema, array $output): array {
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (isset($output[$name]) === false && array_key_exists($name, $output) === false) {
					$output[$name] = $attribute->getDefault();
				}
				if ($filter = $attribute->getFilter('type')) {
					$output[$name] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->output($output[$name]);
				}
				if ($filter = $attribute->getFilter('filter')) {
					$output[$name] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->output($output[$name]);
				}
			}
			return $output;
		}

		/** @inheritdoc */
		public function params(IQuery $query, array $binds = []): array {
			$params = [];
			foreach ($query->params($binds) as $name => $param) {
				$attribute = $query->getSchema($param->getAlias())->getAttribute($param->getProperty());
				$params[$hash = $param->getName()] = is_iterable($value = $param->getValue()) ?
					array_map(function ($value) use ($attribute) {
						return $this->value($attribute, $value);
					}, (is_array($value) ? $value : iterator_to_array($value))) :
					$this->value($attribute, $value);
			}
			return $query->params($params);
		}

		/**
		 * @param IAttribute $attribute
		 * @param mixed      $value
		 *
		 * @return mixed
		 *
		 * @throws FilterException
		 */
		protected function value(IAttribute $attribute, $value) {
			if ($filter = $attribute->getFilter('type')) {
				$value = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($value);
			}
			/**
			 * common filter support; filter name is used for both directions
			 */
			if ($filter = $attribute->getFilter('filter')) {
				$value = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($value);
			}
			return $value;
		}
	}
