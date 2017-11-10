<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityQueue;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\IRelation;
		use Edde\Api\Storage\Query\IQuery;
		use Edde\Common\Entity\Query\LinkQuery;
		use Edde\Common\Entity\Query\RelationQuery;
		use Edde\Common\Entity\Query\UnlinkQuery;
		use Edde\Common\Object\Object;

		class EntityQueue extends Object implements IEntityQueue {
			/** @var IEntity[] */
			protected $entities;
			/** @var IQuery[] */
			protected $queries = [];

			/**
			 * @inheritdoc
			 */
			public function queue(IEntity $entity): IEntityQueue {
				$this->entities[$entity->getHash()] = $entity;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function link(IEntity $from, IEntity $to, ILink $link): IEntityQueue {
				$this->queue($from);
				$this->queue($to);
				/**
				 * maintain 1:N relation, thus create a new link, remove the old one
				 */
				$this->queries[] = new LinkQuery($from, $link, $to);
				$this->unlink($from, $link);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function unlink(IEntity $entity, ILink $link): IEntityQueue {
				/**
				 * generate kind of unique key to have just one unlink per entity type/guid
				 */
				$this->queries[] = new UnlinkQuery($entity, $link);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function attach(IEntity $entity, IEntity $target, IEntity $using, IRelation $relation): IEntityQueue {
				$this->queue($entity);
				$this->queue($target);
				$this->queue($using);
				$this->queries[] = new RelationQuery($entity, $target, $using, $relation);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function isEmpty(): bool {
				return empty($this->entities) && empty($this->entityLinks) && empty($this->entityUnlinks) && empty($this->entityRelations);
			}

			/**
			 * @inheritdoc
			 */
			public function commit(): IEntityQueue {
				foreach ($this->entities as $entity) {
					$entity->commit();
				}
				$this->entities = [];
				$this->queries = [];
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
			public function getQueries(): array {
				return $this->queries;
			}

			/**
			 * @inheritdoc
			 */
			public function getIterator() {
				yield from $this->entities;
			}

			/**
			 * @inheritdoc
			 */
			public function __clone() {
				parent::__clone();
				$this->entities = [];
				$this->queries = [];
			}
		}
