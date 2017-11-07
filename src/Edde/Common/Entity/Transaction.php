<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityLink;
		use Edde\Api\Entity\ITransaction;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\TransactionQuery;

		class Transaction extends Object implements ITransaction {
			use Storage;
			/**
			 * @var IEntity[]
			 */
			protected $entities = [];
			/**
			 * @var IEntityLink[]
			 */
			protected $entityLinks = [];

			/**
			 * @inheritdoc
			 */
			public function entity(IEntity $entity): ITransaction {
				$this->entities[] = $entity;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getEntities(): array {
				return $this->entities;
			}

			/**
			 * @inheritdoc
			 */
			public function link(IEntity $from, IEntity $to, ILink $link): ITransaction {
				$this->entityLinks[] = new EntityLink($from, $to, $link);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function unlink(IEntity $from, IEntity $to): ITransaction {
				throw new \Exception('not supported yet: ' . __METHOD__);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getEntityLinks(): array {
				return $this->entityLinks;
			}

			/**
			 * @inheritdoc
			 */
			public function isEmpty(): bool {
				return empty($this->entities) && empty($this->entityLinks);
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
				foreach ($this->entities as $entity) {
					$entity->commit();
				}
				$this->entities = [];
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): ITransaction {
				$this->entities = [];
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function __clone() {
				parent::__clone();
				$this->entities = [];
			}
		}
