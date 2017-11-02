<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use IteratorAggregate;
		use Traversable;

		/**
		 * A collection is read-only result of some (usually selection) query.
		 */
		interface ICollection extends IteratorAggregate {
			/**
			 * get the query to customize this collection
			 *
			 * @return ISelectQuery
			 */
			public function getQuery(): ISelectQuery;

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
