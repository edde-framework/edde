<?php
	declare(strict_types=1);
	namespace Edde\Hydrator;

	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Validator\ValidatorManager;

	class SchemaHydrator extends AbstractHydrator {
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

		/** @inheritdoc */
		public function hydrate(array $source) {
		}

		/** @inheritdoc */
		public function input(string $name, array $input): array {
			$schema = $this->schemaManager->getSchema($name);
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (($generator = $attribute->getFilter('generator')) && isset($input[$name]) === false) {
					$input[$name] = $this->filterManager->getFilter($this->prefix . ':' . $generator)->input(null);
				}
				$input[$name] = $input[$name] ?? $attribute->getDefault();
				if ($validator = $attribute->getValidator()) {
					$this->validatorManager->validate($this->prefix . ':' . $validator, $input[$name], (object)[
						'name'     => $schema->getName() . '::' . $name,
						'required' => $attribute->isRequired(),
					]);
				}
				if ($filter = $attribute->getFilter('type')) {
					$input[$name] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($input[$name]);
				}
				/**
				 * common filter support; filter name is used for both directions
				 */
				if ($filter = $attribute->getFilter('filter')) {
					$input[$name] = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($input[$name]);
				}
			}
			return $input;
		}

		/** @inheritdoc */
		public function update(string $name, array $update): array {
			$schema = $this->schemaManager->getSchema($name);
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
		public function output(string $name, array $output): array {
			$schema = $this->schemaManager->getSchema($name);
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
	}
