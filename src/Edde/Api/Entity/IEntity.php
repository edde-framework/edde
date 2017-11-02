<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Crate\ICrate;
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
			 * attach the given entity to $this one using m:n relation; returned entity is the
			 * relation entity
			 *
			 * @param IEntity $entity
			 *
			 * @return IEntity
			 */
			public function attach(IEntity $entity): IEntity;

			/**
			 * mark the given entity as related to this one (to save the tree
			 * of entities)
			 *
			 * @param IEntity $entity
			 *
			 * @return IEntity
			 */
			public function related(IEntity $entity): IEntity;

			/**
			 * reverse direction of the related method
			 *
			 * @param IEntity $entity
			 *
			 * @return IEntity
			 */
			public function relatedTo(IEntity $entity): IEntity;

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
			 * load the given data (they should be also filtered)
			 *
			 * @param array $source
			 *
			 * @return IEntity
			 */
			public function load(array $source): IEntity;

			/**
			 * prepare m:n collection of related entities
			 *
			 * @param string $schema
			 *
			 * @return ICollection
			 */
			public function collectionOf(string $schema) : ICollection;

			/**
			 * is the entity loaded from storage, thus it exists?
			 *
			 * @return bool
			 */
			public function exists(): bool;
		}
