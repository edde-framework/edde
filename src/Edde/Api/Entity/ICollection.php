<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Query\IQuery;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use IteratorAggregate;
		use Traversable;

		/**
		 * A collection is read-only result of some (usually selection) query.
		 */
		interface ICollection extends IteratorAggregate {
			/**
			 * set custom query for this collection
			 *
			 * @param IQuery $query
			 *
			 * @return ICollection
			 */
			public function query(IQuery $query): ICollection;

			/**
			 * get the query to customize this collection
			 *
			 * @return IQuery
			 */
			public function getQuery(): IQuery;

			/**
			 * get exactly one entity or throw an exception of the collection is empty; this
			 * method should NOT be used for iteration
			 *
			 * @return IEntity
			 *
			 * @throws EntityNotFoundException
			 */
			public function getEntity(): IEntity;

			/**
			 * a bit magical method which try to find an entity by primary keys and all
			 * unique keys
			 *
			 * @param mixed $name
			 *
			 * @return IEntity
			 *
			 * @throws EntityNotFoundException
			 */
			public function entity($name): IEntity;

			/**
			 * @return Traversable|IEntity[]
			 */
			public function getIterator();
		}
