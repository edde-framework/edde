<?php
	declare(strict_types=1);
	namespace Edde\Crate;

	use Edde\Schema\ISchema;
	use Edde\SimpleObject;
	use stdClass;

	class Crate extends SimpleObject implements ICrate {
		/** @var ISchema */
		protected $schema;
		/** @var IProperty[] */
		protected $properties = [];
		/** @var IProperty */
		protected $primary = null;

		/**
		 * @param ISchema $schema
		 */
		public function __construct(ISchema $schema) {
			$this->schema = $schema;
		}

		/** @inheritdoc */
		public function getSchema(): ISchema {
			return $this->schema;
		}

		/** @inheritdoc */
		public function getPrimary(): IProperty {
			return $this->primary ?: $this->primary = $this->getProperty($this->schema->getPrimary()->getName());
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
				$this->properties[$name] = new Property($this->schema->getAttribute($name));
			}
			return $this->properties[$name];
		}

		/** @inheritdoc */
		public function put(stdClass $source): ICrate {
			$this->properties = [];
			$this->primary = null;
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
			return $this;
		}

		/** @inheritdoc */
		public function isDirty(): bool {
			foreach ($this->properties as $property) {
				if ($property->isDirty()) {
					return true;
				}
			}
			return false;
		}

		/** @inheritdoc */
		public function getDirtyProperties(): array {
			$dirties = [];
			foreach ($this->properties as $name => $property) {
				if ($property->isDirty()) {
					$dirties[$name] = $property;
				}
			}
			return $dirties;
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
			$stdClass = new stdClass();
			foreach ($this->schema->getAttributes() as $k => $property) {
				$stdClass->$k = $this->get($k, $property->getDefault());
			}
			return $stdClass;
		}
	}
