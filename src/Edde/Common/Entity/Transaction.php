<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityLink;
		use Edde\Api\Entity\IEntityUnlink;
		use Edde\Api\Entity\ITransaction;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\IRelation;
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
			 * @var IEntityUnlink[]
			 */
			protected $entityUnlinks = [];
			protected $entityRelaitons = [];

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
				/**
				 * maintain 1:N relation, thus create a new link, remove the old one
				 */
				$this->entityLinks[$from->getPrimary()->get() . $from->getSchema()->getName() . $link->getName() . $link->getTo()->getName()] = new EntityLink($from, $link, $to);
				$this->unlink($from, $link);
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
			public function unlink(IEntity $entity, ILink $link): ITransaction {
				/**
				 * generate kind of unique key to have just one unlink per entity type/guid
				 */
				$this->entityUnlinks[$entity->getPrimary()->get() . $entity->getSchema()->getName() . $link->getName() . $link->getTo()->getName()] = new EntityUnlink($entity, $link);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getEntityUnlinks(): array {
				return $this->entityUnlinks;
			}

			/**
			 * @inheritdoc
			 */
			public function attach(IEntity $entity, IEntity $target, IEntity $using, IRelation $relation): ITransaction {
				$this->entityRelaitons[] = new EntityRelation($entity, $target, $using, $relation);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getEntityRelations(): array {
				return $this->entityRelaitons;
			}

			/**
			 * @inheritdoc
			 */
			public function isEmpty(): bool {
				return empty($this->entities) && empty($this->entityLinks) && empty($this->entityUnlinks);
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
				$this->entityLinks = [];
				$this->entityUnlinks = [];
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(): ITransaction {
				$this->entities = [];
				$this->entityLinks = [];
				$this->entityUnlinks = [];
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function __clone() {
				parent::__clone();
				$this->entities = [];
				$this->entityLinks = [];
				$this->entityUnlinks = [];
			}
		}
