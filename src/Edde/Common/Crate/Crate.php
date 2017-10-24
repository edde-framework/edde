<?php
	namespace Edde\Common\Crate;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Crate\IProperty;
		use Edde\Common\Object\Object;

		class Crate extends Object implements ICrate {
			/**
			 * @var IProperty[]
			 */
			protected $propertyList = [];

			/**
			 * @inheritdoc
			 */
			public function set(string $property, $value): ICrate {
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
				return isset($this->propertyList[$name]);
			}

			/**
			 * @inheritdoc
			 */
			public function getProperty(string $name): IProperty {
				if (isset($this->propertyList[$name]) === false) {
					$this->propertyList[$name] = new Property($name);
				}
				return $this->propertyList[$name];
			}

			/**
			 * @inheritdoc
			 */
			public function update(array $source): ICrate {
				foreach ($source as $k => $v) {
					$this->getProperty($k)->setValue($v);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function push(array $source): ICrate {
				foreach ($source as $k => $v) {
					$this->getProperty($k)->setDefault($v);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): ICrate {
				foreach ($this->propertyList as $property) {
					$property->commit();
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function isDirty(): bool {
				foreach ($this->propertyList as $property) {
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
				foreach ($this->propertyList as $name => $property) {
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
				foreach ($this->propertyList as $property) {
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
				foreach ($this->propertyList as $name => $property) {
					$source[$name] = $property->get();
				}
				return $source;
			}
		}
