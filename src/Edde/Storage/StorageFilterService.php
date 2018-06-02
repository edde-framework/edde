<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Edde;
	use Edde\Schema\ISchema;
	use Edde\Service\Filter\FilterManager;
	use Edde\Service\Validator\ValidatorManager;
	use stdClass;

	class StorageFilterService extends Edde implements IStorageFilterService {
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
		public function input(ISchema $schema, stdClass $input): stdClass {
			$stdClass = clone $input;
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (($generator = $attribute->getFilter('generator')) && $stdClass->$name === null) {
					$stdClass->$name = $this->filterManager->getFilter($this->prefix . ':' . $generator)->input(null);
				}
				$stdClass->$name = $stdClass->$name ?: $attribute->getDefault();
				if ($validator = $attribute->getValidator()) {
					$this->validatorManager->validate($this->prefix . ':' . $validator, $stdClass->$name, (object)[
						'name'     => $schema->getName() . '::' . $name,
						'required' => $attribute->isRequired(),
					]);
				}
				if ($filter = $attribute->getFilter('type')) {
					$stdClass->$name = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($stdClass->$name);
				}
				/**
				 * common filter support; filter name is used for both directions
				 */
				if ($filter = $attribute->getFilter('filter')) {
					$stdClass->$name = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($stdClass->$name);
				}
			}
			return $stdClass;
		}

		/** @inheritdoc */
		public function update(ISchema $schema, stdClass $update): stdClass {
			$stdClass = clone $update;
			foreach ($schema->getAttributes() as $name => $attribute) {
				if ($validator = $attribute->getValidator()) {
					$this->validatorManager->validate($this->prefix . ':' . $validator, $stdClass->$name, (object)[
						'name'     => $schema->getName() . '::' . $name,
						'required' => $attribute->isRequired(),
					]);
				}
				if ($filter = $attribute->getFilter('type')) {
					$stdClass->$name = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($stdClass->$name);
				}
				/**
				 * common filter support; filter name is used for both directions
				 */
				if ($filter = $attribute->getFilter('filter')) {
					$stdClass->$name = $this->filterManager->getFilter($this->prefix . ':' . $filter)->input($stdClass->$name);
				}
			}
			return $stdClass;
		}

		/** @inheritdoc */
		public function output(ISchema $schema, stdClass $output): stdClass {
			$stdClass = clone $output;
			foreach ($schema->getAttributes() as $name => $attribute) {
				if (property_exists($stdClass, $name) === false) {
					$stdClass->$name = $attribute->getDefault();
				}
				if ($filter = $attribute->getFilter('type')) {
					$stdClass->$name = $this->filterManager->getFilter($this->prefix . ':' . $filter)->output($stdClass->$name);
				}
				if ($filter = $attribute->getFilter('filter')) {
					$stdClass->$name = $this->filterManager->getFilter($this->prefix . ':' . $filter)->output($stdClass->$name);
				}
			}
			return $stdClass;
		}
	}
