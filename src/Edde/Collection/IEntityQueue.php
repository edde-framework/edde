<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Storage\IStorage;
	use Edde\Storage\StorageException;

	interface IEntityQueue {
		/**
		 * mark the given entity for save
		 *
		 * @param IEntity $entity
		 *
		 * @return IEntityQueue
		 */
		public function save(IEntity $entity): IEntityQueue;

		/**
		 * attach target entity to source using the given relation; relation entity with relation schema is returned
		 *
		 * @param IEntity $entity
		 * @param IEntity $target
		 * @param string  $relation
		 *
		 * @return IEntityQueue
		 */
		public function attach(IEntity $entity, IEntity $target, string $relation): IEntityQueue;

		/**
		 * remove relation between given entity and it's target
		 *
		 * @param IEntity $entity
		 * @param IEntity $target
		 * @param string  $relation
		 *
		 * @return IEntityQueue
		 */
		public function detach(IEntity $entity, IEntity $target, string $relation): IEntityQueue;

		/**
		 * link target entity to source; that means all entities are detached (by query, thus it's not possible to catch individual entities) and
		 * attach a new one
		 *
		 * @param IEntity $entity
		 * @param IEntity $target
		 * @param string  $relation
		 *
		 * @return IEntityQueue
		 */
		public function link(IEntity $entity, IEntity $target, string $relation): IEntityQueue;

		/**
		 * remove all relations between the given entity and target schema
		 *
		 * @param IEntity $entity
		 * @param string  $target
		 * @param string  $relation
		 *
		 * @return IEntityQueue
		 */
		public function disconnect(IEntity $entity, string $target, string $relation): IEntityQueue;

		/**
		 * commit all changes in a queue to the given storage; commit should run in a transaction
		 *
		 * @param IStorage $storage
		 *
		 * @return IEntityQueue
		 *
		 * @throws StorageException
		 */
		public function commit(IStorage $storage): IEntityQueue;
	}
