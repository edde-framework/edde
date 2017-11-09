<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\IRelation;
		use IteratorAggregate;

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
			 * @return IEntityLink[]
			 */
			public function getEntityLinks(): array;

			/**
			 * @return IEntityUnlink[]
			 */
			public function getEntityUnlinks(): array;

			/**
			 * @return IEntityRelation[]
			 */
			public function getEntityRelations(): array;

			/**
			 * @return \Traversable|IEntity[]
			 */
			public function getIterator();
		}
