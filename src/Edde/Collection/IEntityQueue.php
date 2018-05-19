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
