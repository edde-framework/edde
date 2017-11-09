<?php
	namespace Edde\Common\Entity;

		use Edde\Api\Entity\IEntity;
		use Edde\Api\Entity\IEntityLink;
		use Edde\Api\Entity\IEntityQueue;
		use Edde\Api\Entity\IEntityRelation;
		use Edde\Api\Entity\IEntityUnlink;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\IRelation;
		use Edde\Common\Object\Object;

		class EntityQueue extends Object implements IEntityQueue {
			/** @var IEntity[] */
			protected $entities;
			/** @var IEntityLink[] */
			protected $entityLinks = [];
			/** @var IEntityUnlink[] */
			protected $entityUnlinks = [];
			/** @var IEntityRelation[] */
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
				/**
				 * maintain 1:N relation, thus create a new link, remove the old one
				 */
				$this->entityLinks[$from->getHash() . $from->getSchema()->getName() . $link->getName() . $link->getTo()->getName()] = new EntityLink($from, $link, $to);
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
				$this->entityUnlinks[$entity->getHash() . $entity->getSchema()->getName() . $link->getName() . $link->getTo()->getName()] = new EntityUnlink($entity, $link);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function attach(IEntity $entity, IEntity $target, IEntity $using, IRelation $relation): IEntityQueue {
				$this->entityRelations[] = new EntityRelation($entity, $target, $using, $relation);
				return $this;
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
			public function commit(): IEntityQueue {
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
			}
		}
