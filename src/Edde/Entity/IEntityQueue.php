<?php
	declare(strict_types=1);
	namespace Edde\Entity;

	use Edde\Query\IDetachQuery;
	use Edde\Query\IDisconnectQuery;
	use Edde\Query\IQuery;
	use Edde\Schema\ILink;
	use Edde\Schema\IRelation;
	use IteratorAggregate;
	use Traversable;

	interface IEntityQueue extends IteratorAggregate {
		/**
		 * queue the given entity
		 *
		 * @param IEntity $entity
		 *
		 * @return IEntityQueue
		 */
		public function queue(IEntity $entity): IEntityQueue;

		/**
		 * make a link between the source and target entity
		 *
		 * @param IEntity $from
		 * @param IEntity $to
		 * @param ILink   $link
		 *
		 * @return IEntityQueue
		 */
		public function link(IEntity $from, IEntity $to, ILink $link): IEntityQueue;

		/**
		 * remove relation (1:n) to the given schema
		 *
		 * @param IEntity $entity
		 * @param ILink   $link
		 *
		 * @return IEntityQueue
		 */
		public function unlink(IEntity $entity, ILink $link): IEntityQueue;

		/**
		 * attach two entities using relation entity (m:n)
		 *
		 * @param IEntity   $entity   use this entity
		 * @param IEntity   $target   join this entity
		 * @param IEntity   $using    through this relational entity
		 * @param IRelation $relation using thins relation
		 *
		 * @return IEntityQueue
		 */
		public function attach(IEntity $entity, IEntity $target, IEntity $using, IRelation $relation): IEntityQueue;

		/**
		 * delete all relations to the given entity
		 *
		 * @param IEntity   $entity
		 * @param IEntity   $target
		 * @param IRelation $relation
		 *
		 * @return \Edde\Query\IDetachQuery
		 */
		public function detach(IEntity $entity, IEntity $target, IRelation $relation): IDetachQuery;

		/**
		 * @param IEntity   $entity
		 * @param IRelation $relation
		 *
		 * @return IDisconnectQuery
		 */
		public function disconnect(IEntity $entity, IRelation $relation): IDisconnectQuery;

		/**
		 * mark the given entity for deletation
		 *
		 * @param IEntity $entity
		 *
		 * @return IEntityQueue
		 */
		public function delete(IEntity $entity): IEntityQueue;

		/**
		 * is there something to do?
		 *
		 * @return bool
		 */
		public function isEmpty(): bool;

		/**
		 * execute commit on all entities and clear the queue
		 *
		 * @return IEntityQueue
		 */
		public function commit(): IEntityQueue;

		/**
		 * @return IEntity[]
		 */
		public function getEntities(): array;

		/**
		 * return queries to be executed (link, unlink, attach, delete, ...)
		 *
		 * @return IQuery[]
		 */
		public function getQueries(): array;

		/**
		 * @return Traversable|IEntity[]
		 */
		public function getIterator();
	}