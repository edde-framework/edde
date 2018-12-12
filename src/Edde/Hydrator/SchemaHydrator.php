<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Validator\ValidatorManager;
	use function array_key_exists;

	class SchemaHydrator extends AbstractHydrator {
		use SchemaManager;
		use FilterManager;
		use ValidatorManager;
		/** @var string|null */
		protected $name;
		/** @var string */
		protected $prefix;

		/**
		 * @param string|null $name
		 * @param string      $prefix
		 */
		public function __construct(string $name = null, string $prefix = 'storage') {
			$this->name = $name;
			$this->prefix = $prefix;
		}

		/** @inheritdoc */
		public function hydrate(array $source) {
			if ($this->name) {
				return $this->input($this->name, $source);
			}
			return $source;
		}

		/** @inheritdoc */
		public function input(string $name, array $input): array {
			$schema = $this->schemaManager->getSchema($name);
			foreach (array_keys(array_diff_key($input, $schema->getAttributes())) as $diff) {
				unset($input[$diff]);
			}
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (isset($input[$name]) === false && array_key_exists($name, $input) === false) {
					$input[$name] = $attribute->getDefault();
				}
				if ($filter = $attribute->getFilter('type')) {
					$input[$name] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($input[$name]);
				}
				if ($filter = $attribute->getFilter('filter')) {
					$input[$name] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($input[$name]);
				}
			}
			return $input;
		}

		/** @inheritdoc */
		public function update(string $name, array $update): array {
			$schema = $this->schemaManager->getSchema($name);
			foreach (array_keys(array_diff_key($update, $schema->getAttributes())) as $diff) {
				unset($update[$diff]);
			}
			foreach ($update as $k => $v) {
				$attribute = $schema->getAttribute($k);
				if ($validator = $attribute->getValidator()) {
					$this->validatorManager->validate($this->prefix . ':' . $validator, $v, [
						'name'     => $schema->getName() . '::' . $k,
						'required' => $attribute->isRequired(),
					]);
				}
				if ($filter = $attribute->getFilter('type')) {
					$update[$k] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->output($v);
				}
				/**
				 * common filter support; filter name is used for both directions
				 */
				if ($filter = $attribute->getFilter('filter')) {
					$update[$k] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->output($v);
				}
			}
			return $update;
		}

		/** @inheritdoc */
		public function output(string $name, array $output): array {
			$schema = $this->schemaManager->getSchema($name);
			foreach (array_keys(array_diff_key($output, $schema->getAttributes())) as $diff) {
				unset($output[$diff]);
			}
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (($generator = $attribute->getFilter('generator')) && isset($output[$name]) === false) {
					$output[$name] = $this->filterManager->getFilter($this->prefix . ':' . $generator)->output(null);
				}
				$output[$name] = isset($output[$name]) || array_key_exists($name, $output) ? $output[$name] : $attribute->getDefault();
				if ($validator = $attribute->getValidator()) {
					$this->validatorManager->validate($this->prefix . ':' . $validator, $output[$name], [
						'name'     => $schema->getName() . '::' . $name,
						'required' => $attribute->isRequired(),
					]);
				}
				if ($filter = $attribute->getFilter('type')) {
					$output[$name] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->output($output[$name]);
				}
				/**
				 * common filter support; filter name is used for both directions
				 */
				if ($filter = $attribute->getFilter('filter')) {
					$output[$name] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->output($output[$name]);
				}
			}
			return $output;
		}
	}
