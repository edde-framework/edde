<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityQueue;
		use Edde\Api\Entity\ITransaction;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\TransactionQuery;

		class Transaction extends Object implements ITransaction {
			/** @var IStorage */
			protected $storage;
			/** @var IEntityQueue */
			protected $entityQueue;

			public function __construct(IStorage $storage, IEntityQueue $entityQueue) {
				$this->storage = $storage;
				$this->entityQueue = $entityQueue;
			}

			/**
			 * @inheritdoc
			 */
			public function queue(IEntity $entity): ITransaction {
				$this->entityQueue->queue($entity);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getEntityQueue(): IEntityQueue {
				return $this->entityQueue;
			}

			/**
			 * @inheritdoc
			 */
			public function isEmpty(): bool {
				return $this->entityQueue->isEmpty();
			}

			/**
			 * @inheritdoc
			 */
			public function execute(): ITransaction {
				try {
					$this->storage->start();
					$this->storage->execute(new TransactionQuery($this));
					$this->storage->commit();
					$this->entityQueue->commit();
					return $this;
				} catch (\Throwable $exception) {
					$this->storage->rollback();
					throw $exception;
				}
			}
		}
