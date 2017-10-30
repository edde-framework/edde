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
		}
