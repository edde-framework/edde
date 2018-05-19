<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\SimpleObject;
	use Edde\Storage\IStorage;

	class EntityQueue extends SimpleObject implements IEntityQueue {
		protected $entityQueue = [];

		/** @inheritdoc */
		public function save(IEntity $entity): IEntityQueue {
			$this->entityQueue[] = [
				'save',
				$entity,
			];
			return $this;
		}

		/** @inheritdoc */
		public function commit(IStorage $storage): IEntityQueue {
			$storage->transaction(function () use ($storage) {
				foreach ($this->entityQueue as $entityQueue) {
					/** @var $entity IEntity */
					[$type, $entity] = $entityQueue;
					switch ($type) {
						case 'save':
							$entity->push($storage->save($entity->getSchema()->getName(), $entity->toObject()));
							break;
					}
				}
			});
			return $this;
		}
	}
