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
			 * @return \Traversable|IEntity[]
			 */
			public function getIterator();
		}
