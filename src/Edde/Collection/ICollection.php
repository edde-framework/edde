<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Query\IQuery;
	use Edde\Query\QueryException;
	use Edde\Storage\StorageException;
	use IteratorAggregate;
	use Traversable;

	/**
	 * A collection is read-only result of some (usually selection) query.
	 */
	interface ICollection extends IteratorAggregate {
		/**
		 * return current select query of a collection
		 *
		 * @return IQuery
		 */
		public function getQuery(): IQuery;

		/**
		 * proxy to IQuery method
		 *
		 * @param string      $schema
		 * @param string|null $alias
		 *
		 * @return ICollection
		 */
		public function select(string $schema, string $alias = null): ICollection;

		/**
		 * @param string[] $schemas
		 *
		 * @return ICollection
		 */
		public function selects(array $schemas): ICollection;

		/**
		 * create all schemas in this collection (simply, CREATE TABLE ...)
		 *
		 * thus should run in exclusive transaction as some database systems has
		 * problems with schema & data modifications in one transaction
		 *
		 * @return ICollection
		 *
		 * @throws StorageException
		 */
		public function create(): ICollection;

		/**
		 * return count of the given alias
		 *
		 * @param string $alias
		 *
		 * @return int
		 *
		 * @throws QueryException
		 * @throws StorageException
		 */
		public function count(string $alias): int;

		/**
		 * @return Traversable|IRecord[]
		 */
		public function getIterator();
	}
