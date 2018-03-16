<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Object;
	use Edde\Query\DeleteQuery;
	use Edde\Query\DetachQuery;
	use Edde\Query\DisconnectQuery;
	use Edde\Query\IDetachQuery;
	use Edde\Query\IDisconnectQuery;
	use Edde\Query\IQuery;
	use Edde\Query\LinkQuery;
	use Edde\Query\RelationQuery;
	use Edde\Query\UnlinkQuery;
	use Edde\Schema\ILink;
	use Edde\Schema\IRelation;

	class EntityQueue extends Object implements IEntityQueue {
		/** @var IEntity[] */
		protected $entities;
		/** @var \Edde\Query\IQuery[] */
		protected $queries = [];

		/** @inheritdoc */
		public function queue(IEntity $entity): IEntityQueue {
			$this->entities[$entity->getHash()] = $entity;
			return $this;
		}

		/** @inheritdoc */
		public function link(IEntity $from, IEntity $to, ILink $link): IEntityQueue {
			$this->queue($from);
			$this->queue($to);
			/**
			 * maintain 1:N relation, thus create a new link, remove the old one
			 */
			$this->unlink($from, $link);
			$this->queries[] = new LinkQuery($from, $link, $to);
			return $this;
		}

		/** @inheritdoc */
		public function unlink(IEntity $entity, ILink $link): IEntityQueue {
			/**
			 * generate kind of unique key to have just one unlink per entity type/uuid
			 */
			$this->queries[] = new UnlinkQuery($entity, $link);
			return $this;
		}

		/** @inheritdoc */
		public function attach(IEntity $entity, IEntity $target, IEntity $using, IRelation $relation): IEntityQueue {
			$this->queue($entity);
			$this->queue($target);
			$this->queue($using);
			$this->queries[] = new RelationQuery($entity, $target, $using, $relation);
			return $this;
		}

		/** @inheritdoc */
		public function detach(IEntity $entity, IEntity $target, IRelation $relation): IDetachQuery {
			return $this->queries[] = new DetachQuery($entity, $target, $relation);
		}

		/** @inheritdoc */
		public function disconnect(IEntity $entity, IRelation $relation): IDisconnectQuery {
			return $this->queries[] = new DisconnectQuery($entity, $relation);
		}

		/** @inheritdoc */
		public function delete(IEntity $entity): IEntityQueue {
			$this->queries[] = new DeleteQuery($entity);
			return $this;
		}

		/** @inheritdoc */
		public function isEmpty(): bool {
			return empty($this->entities) && empty($this->queries);
		}

		/** @inheritdoc */
		public function commit(): IEntityQueue {
			foreach ($this->entities as $entity) {
				$entity->commit();
			}
			$this->entities = [];
			$this->queries = [];
			return $this;
		}

		/** @inheritdoc */
		public function getEntities(): array {
			return $this->entities;
		}

		/** @inheritdoc */
		public function getQueries(): array {
			return $this->queries;
		}

		/** @inheritdoc */
		public function getIterator() {
			yield from $this->entities;
		}

		/** @inheritdoc */
		public function __clone() {
			parent::__clone();
			$this->entities = [];
			$this->queries = [];
		}
	}
