<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Connection\DuplicateEntryException;
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
		 * @return \Edde\Crate\IProperty
		 *
		 * @throws SchemaException
		 */
		public function getPrimary(): IProperty;

		/**
		 * save this entity and all related entities (entity queue in a transaction)
		 *
		 * @return IEntity
		 *
		 * @throws DuplicateEntryException
		 */
		public function save(): IEntity;

		/**
		 * load the given data (they should be also filtered)
		 *
		 * @param array $source
		 *
		 * @return IEntity
		 */
		public function filter(array $source): IEntity;

		/**
		 * return sanitized array of this entity
		 *
		 * @return array
		 */
		public function sanitize(): array;

		/**
		 * is this entity valid?
		 *
		 * @return bool
		 */
		public function isValid(): bool;

		/**
		 * validate data of an entity
		 *
		 * @return IEntity
		 */
		public function validate(): IEntity;
	}
