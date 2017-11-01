<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use IteratorAggregate;
		use Traversable;

		/**
		 * A collection is read-only result of some (usually selection) query.
		 */
		interface ICollection extends IteratorAggregate {
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
			 * @return Traversable|IEntity[]
			 */
			public function getIterator();
		}