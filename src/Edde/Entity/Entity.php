<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Crate\Crate;
	use stdClass;

	class Entity extends Crate implements IEntity {
		/** @inheritdoc */
		public function save(): IEntity {
			return $this;
		}

		/** @inheritdoc */
		public function toObject(): stdClass {
			$object = new stdClass();
			foreach ($this->schema->getAttributes() as $k => $property) {
				$object->$k = $this->get($k, $property->getDefault());
			}
			return $object;
		}
	}
