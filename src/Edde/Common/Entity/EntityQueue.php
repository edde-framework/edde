<?php
	declare(strict_types=1);
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityQueue;
		use Edde\Api\Entity\Query\ILinkQuery;
		use Edde\Api\Entity\Query\IRelationQuery;
		use Edde\Api\Entity\Query\IUnlinkQuery;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\IRelation;
		use Edde\Common\Entity\Query\LinkQuery;
		use Edde\Common\Entity\Query\RelationQuery;
		use Edde\Common\Entity\Query\UnlinkQuery;
		use Edde\Common\Object\Object;

		class EntityQueue extends Object implements IEntityQueue {
			/** @var IEntity[] */
			protected $entities;
			/** @var ILinkQuery[] */
			protected $entityLinks = [];
			/** @var IUnlinkQuery[] */
			protected $entityUnlinks = [];
			/** @var IRelationQuery[] */
			protected $entityRelations = [];

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
				$this->entityLinks[$from->getHash() . $from->getSchema()->getName() . $link->getName() . $link->getTo()->getName()] = new LinkQuery($from, $link, $to);
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
				$this->entityUnlinks[$entity->getHash() . $entity->getSchema()->getName() . $link->getName() . $link->getTo()->getName()] = new UnlinkQuery($entity, $link);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function attach(IEntity $entity, IEntity $target, IEntity $using, IRelation $relation): IEntityQueue {
				$this->queue($entity);
				$this->queue($target);
				$this->queue($using);
				$hash = sha1($entity->getHash() . $entity->getSchema()->getName() . $target->getHash() . $target->getSchema()->getName() . $using->getHash() . $using->getSchema()->getName());
				$this->entityRelations[$hash] = new RelationQuery($entity, $target, $using, $relation);
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
				$this->entityLinks = [];
				$this->entityUnlinks = [];
				$this->entityRelations = [];
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
			public function getEntityLinks(): array {
				return $this->entityLinks;
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
			public function getEntityRelations(): array {
				return $this->entityRelations;
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
				$this->entityLinks = [];
				$this->entityUnlinks = [];
				$this->entityRelations = [];
			}
		}
