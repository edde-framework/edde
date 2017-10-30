<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Crate\IProperty;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\Exception\StorageException;

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
			 * return list of primary properties
			 *
			 * @return IProperty[]
			 */
			public function getPrimaryList(): array;

			/**
			 * attach the given entity to the given property of this entity
			 *
			 * @param string  $name
			 * @param IEntity $entity
			 *
			 * @return IEntity
			 */
			public function attach(string $name, IEntity $entity): IEntity;

			/**
			 * are there some linked entities to this one?
			 *
			 * @return bool
			 */
			public function hasLinks(): bool;

			/**
			 * get array of linked entities
			 *
			 * @return IEntity[]
			 */
			public function getLinkList(): array;

			/**
			 * save this entity into storage (and all related stuff to this entity)
			 *
			 * @return IEntity
			 *
			 * @throws StorageException
			 * @throws DuplicateEntryException
			 * @throws IntegrityException
			 */
			public function save(): IEntity;

			/**
			 * shortcut to update already existing entity
			 *
			 * @return IEntity
			 */
			public function update(): IEntity;

			/**
			 * force insertion of this entity bypassing existence check
			 *
			 * @return IEntity
			 */
			public function insert(): IEntity;

			/**
			 * return collection of this entity; collection is not related to entity itself
			 *
			 * @return ICollection
			 */
			public function collection(): ICollection;

			/**
			 * create a related entity based on the given schema (m:n relation); the returned entity
			 * is the relation entity (returned entity is not save by default)
			 *
			 * @param IEntity $entity
			 * @param string  $schema
			 *
			 * @return IEntity
			 */
			public function relationTo(IEntity $entity, string $schema): IEntity;
		}
