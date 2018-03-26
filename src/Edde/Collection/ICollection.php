<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use IteratorAggregate;
	use Traversable;

	/**
	 * A collection is read-only result of some (usually selection) query.
	 */
	interface ICollection extends IteratorAggregate {
		/**
		 * @param string $schema
		 * @param string $alias
		 *
		 * @return ICollection
		 */
		public function use(string $schema, string $alias): ICollection;

		/**
		 * create all schemas in this collection (simply, CREATE TABLE ...)
		 *
		 * thus should run in exclusive transaction as some database systems has
		 * problems with schema & data modifications in one transaction
		 *
		 * @return ICollection
		 *
		 * @throws CollectionException
		 */
		public function create(): ICollection;

		/**
		 * @return Traversable|IRecord[]
		 */
		public function getIterator();
	}
