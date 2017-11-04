<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Crate\ICrate;
		use Edde\Api\Schema\IRelation;
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
			 * $this is relation entity which connects the given two entities together
			 * using specified relation (m:n, 1:n included)
			 *
			 * @param IEntity   $entity
			 * @param IEntity   $to
			 * @param IRelation $relation
			 *
			 * @return IEntity
			 */
			public function connect(IEntity $entity, IEntity $to, IRelation $relation): IEntity;

			/**
			 * bind the given entity to current one; bound entities are saved before $this
			 *
			 * @param IEntity $entity
			 *
			 * @return IEntity
			 */
			public function bind(IEntity $entity): IEntity;

			/**
			 * bind to the given entity; $this is saved before bound entities
			 *
			 * @param IEntity $entity
			 *
			 * @return IEntity
			 */
			public function bindTo(IEntity $entity): IEntity;

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
			 * @param string $alias
			 *
			 * @return ICollection
			 */
			public function join(string $schema, string $alias): ICollection;

			/**
			 * is the entity loaded from storage, thus it exists?
			 *
			 * @return bool
			 */
			public function exists(): bool;
		}
