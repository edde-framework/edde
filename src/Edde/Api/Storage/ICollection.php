<?php
	namespace Edde\Api\Storage;

		use Edde\Api\Query\ISelectQuery;
		use Edde\Api\Storage\Exception\EntityNotFoundException;

		interface ICollection extends ISelectQuery, \IteratorAggregate {
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
			 * load an entity by the given value; this method is quite magical and suppose this
			 * collection is in clear state (query is not modified)
			 *
			 * @param mixed $value
			 *
			 * @return IEntity
			 *
			 * @throws EntityNotFoundException
			 */
			public function load($value): IEntity;

			/**
			 * @return \Traversable|IEntity[]
			 */
			public function getIterator();
		}
