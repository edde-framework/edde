<?php
	declare(strict_types=1);
	namespace Edde\Crate;

	use Edde\Object;
	use Edde\Schema\ISchema;
	use stdClass;

	class Crate extends Object implements ICrate {
		/** @var ISchema */
		protected $schema;
		/** @var IProperty[] */
		protected $properties = [];
		/** @var bool */
		protected $dirty = null;

		/**
		 * @param ISchema $schema
		 */
		public function __construct(ISchema $schema) {
			$this->schema = $schema;
		}

		/** @inheritdoc */
		public function set(string $property, $value): ICrate {
			$this->getProperty($property)->setValue($value);
			return $this;
		}

		/** @inheritdoc */
		public function get(string $property, $default = null) {
			return $this->getProperty($property)->get($default);
		}

		/** @inheritdoc */
		public function hasProperty(string $name): bool {
			return isset($this->properties[$name]);
		}

		/** @inheritdoc */
		public function getProperty(string $name): IProperty {
			if (isset($this->properties[$name]) === false) {
				$this->properties[$name] = new Property($name);
			}
			return $this->properties[$name];
		}

		/** @inheritdoc */
		public function put(stdClass $source): ICrate {
			$this->properties = [];
			foreach ($source as $k => $v) {
				$this->getProperty($k)->setValue($v);
			}
			return $this;
		}

		/** @inheritdoc */
		public function push(stdClass $source): ICrate {
			foreach ($source as $k => $v) {
				$this->getProperty($k)->setDefault($v);
			}
			return $this;
		}

		/** @inheritdoc */
		public function commit(): ICrate {
			foreach ($this->properties as $property) {
				$property->commit();
			}
			$this->dirty = null;
			return $this;
		}

		/** @inheritdoc */
		public function setDirty(bool $dirty = true): ICrate {
			$this->dirty = $dirty;
			return $this;
		}

		/** @inheritdoc */
		public function isDirty(): bool {
			if ($this->dirty !== null) {
				return $this->dirty;
			}
			foreach ($this->properties as $property) {
				if ($property->isDirty()) {
					return true;
				}
			}
			return false;
		}

		/** @inheritdoc */
		public function getDirtyProperties(): array {
			$dirtyList = [];
			foreach ($this->properties as $name => $property) {
				if ($property->isDirty()) {
					$dirtyList[$name] = $property;
				}
			}
			return $dirtyList;
		}

		/** @inheritdoc */
		public function isEmpty(): bool {
			foreach ($this->properties as $property) {
				if ($property->isEmpty() === false) {
					return false;
				}
			}
			return true;
		}

		/** @inheritdoc */
		public function toObject(): stdClass {
			$object = new stdClass();
			foreach ($this->properties as $name => $property) {
				$object->$name = $property->get();
			}
			return $object;
		}
	}
