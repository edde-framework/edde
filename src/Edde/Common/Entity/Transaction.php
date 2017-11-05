<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\ITransaction;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\TransactionQuery;

		class Transaction extends Object implements ITransaction {
			use Storage;
			/**
			 * @var IEntity[]
			 */
			protected $entityList = [];

			/**
			 * @inheritdoc
			 */
			public function entity(IEntity $entity): ITransaction {
				$this->entityList[] = $entity;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getEntityList(): array {
				return $this->entityList;
			}

			/**
			 * @inheritdoc
			 */
			public function link(IEntity $from, IEntity $to): ITransaction {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function unlink(IEntity $from, IEntity $to): ITransaction {
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function isEmpty(): bool {
				return empty($this->entityList);
			}

			/**
			 * @inheritdoc
			 */
			public function execute(): ITransaction {
				if ($this->isEmpty()) {
					return $this;
				}
				$this->storage->start();
				try {
					$this->storage->execute(new TransactionQuery($this));
					$this->storage->commit();
					$this->commit();
				} catch (\Throwable $exception) {
					$this->storage->rollback();
					throw $exception;
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): ITransaction {
				foreach ($this->entityList as $entity) {
					$entity->commit();
				}
				$this->entityList = [];
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): ITransaction {
				$this->entityList = [];
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function __clone() {
				parent::__clone();
				$this->entityList = [];
			}
		}
