<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Crate\Crate;
	use Edde\Crate\ICrate;
	use Edde\Crate\IProperty;
	use Edde\Schema\ISchema;
	use stdClass;

	class Entity extends Crate implements IEntity {
		/** @var IProperty */
		protected $primary = null;

		/** @inheritdoc */
		public function getSchema(): ISchema {
			return $this->schema;
		}

		/** @inheritdoc */
		public function getPrimary(): IProperty {
			return $this->primary ?: $this->primary = $this->getProperty($this->schema->getPrimary()->getName());
		}

		/** @inheritdoc */
		public function put(stdClass $source): ICrate {
			$this->primary = null;
			return parent::put($source);
		}

		/** @inheritdoc */
		public function save(): IEntity {
			return $this;
		}

		/** @inheritdoc */
		public function toObject(): stdClass {
			$object = new stdClass();
			foreach ($this->schema->getProperties() as $k => $property) {
				$object->$k = $this->get($k, $property->getDefault());
			}
			return $object;
		}
	}
