<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Crate\ICrate;
	use Edde\Crate\IProperty;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;

	/**
	 * An Entity is extended Crate with some additional features.
	 */
	interface IEntity extends ICrate {
		/**
		 * get entity's schema
		 *
		 * @return ISchema
		 */
		public function getSchema(): ISchema;

		/**
		 * return primary property of this entity
		 *
		 * @return IProperty
		 *
		 * @throws SchemaException
		 */
		public function getPrimary(): IProperty;

		/**
		 * persist changes marked in this entity (update, delete, ...)
		 *
		 * @return IEntity
		 */
		public function save(): IEntity;
	}
