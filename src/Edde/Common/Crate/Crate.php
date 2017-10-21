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
			public function hasProperty(string $name): bool {
				return isset($this->propertyList[$name]);
			}

			/**
			 * @inheritdoc
			 */
			public function getProperty(string $name): IProperty {
				if (isset($this->propertyList[$name]) === false) {
				}
				return $this->propertyList[$name];
			}

			/**
			 * @inheritdoc
			 */
			public function update(array $source): ICrate {
				foreach ($source as $k => $v) {
					$this->getProperty($k)->default($v);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function push(array $source): ICrate {
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
			public function getDirtyList(): array {
				$dirtyList = [];
				if ($this->isDirty() === false) {
					return [];
				}
				foreach ($this->propertyList as $property) {
					if ($property->isDirty()) {
						$dirtyList[] = $property;
					}
				}
				return $dirtyList;
			}
		}
