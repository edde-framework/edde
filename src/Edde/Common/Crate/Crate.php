<?php
	declare(strict_types=1);
	namespace Edde\Common\Crate;

	use Edde\Crate\ICrate;
	use Edde\Crate\IProperty;
	use Edde\Object;

	class Crate extends Object implements \Edde\Crate\ICrate {
		/**
		 * @var IProperty[]
		 */
		protected $properties = [];
		/**
		 * @var bool
		 */
		protected $dirty = null;

		/**
		 * @inheritdoc
		 */
		public function set(string $property, $value): \Edde\Crate\ICrate {
			$this->getProperty($property)->setValue($value);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $property, $default = null) {
			return $this->getProperty($property)->get($default);
		}

		/**
		 * @inheritdoc
		 */
		public function hasProperty(string $name): bool {
			return isset($this->properties[$name]);
		}

		/**
		 * @inheritdoc
		 */
		public function getProperty(string $name): IProperty {
			if (isset($this->properties[$name]) === false) {
				$this->properties[$name] = new Property($name);
			}
			return $this->properties[$name];
		}

		/**
		 * @inheritdoc
		 */
		public function put(array $source): ICrate {
			foreach ($source as $k => $v) {
				$this->getProperty($k)->setValue($v);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function push(array $source): \Edde\Crate\ICrate {
			foreach ($source as $k => $v) {
				$this->getProperty($k)->setDefault($v);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function commit(): \Edde\Crate\ICrate {
			foreach ($this->properties as $property) {
				$property->commit();
			}
			$this->dirty = null;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setDirty(bool $dirty = true): \Edde\Crate\ICrate {
			$this->dirty = $dirty;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
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

		/**
		 * @inheritdoc
		 */
		public function getDirtyProperties(): array {
			$dirtyList = [];
			foreach ($this->properties as $name => $property) {
				if ($property->isDirty()) {
					$dirtyList[$name] = $property;
				}
			}
			return $dirtyList;
		}

		/**
		 * @inheritdoc
		 */
		public function isEmpty(): bool {
			foreach ($this->properties as $property) {
				if ($property->isEmpty() === false) {
					return false;
				}
			}
			return true;
		}

		/**
		 * @inheritdoc
		 */
		public function toArray(): array {
			$source = [];
			foreach ($this->properties as $name => $property) {
				$source[$name] = $property->get();
			}
			return $source;
		}

		public function __clone() {
			parent::__clone();
			$this->properties = [];
			$this->dirty = null;
		}
	}
