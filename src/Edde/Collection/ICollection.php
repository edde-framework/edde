<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Query\ISelectQuery;
	use Edde\Query\QueryException;
	use Edde\Schema\SchemaException;
	use Edde\Storage\StorageException;
	use IteratorAggregate;
	use stdClass;
	use Traversable;

	/**
	 * A collection is read-only result of some (usually selection) query.
	 */
	interface ICollection extends IteratorAggregate {
		/**
		 * return current select query of a collection
		 *
		 * @return ISelectQuery
		 */
		public function getSelectQuery(): ISelectQuery;

		/**
		 * @param string      $schema
		 * @param string|null $alias
		 *
		 * @return ICollection
		 */
		public function use(string $schema, string $alias = null): ICollection;

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
		 * insert a new item into a schema represented by the given alias
		 *
		 * @param string   $alias
		 * @param stdClass $source
		 *
		 * @return IEntity return newly create entity
		 *
		 * @throws StorageException
		 * @throws QueryException
		 * @throws SchemaException
		 */
		public function insert(string $alias, stdClass $source): IEntity;

		/**
		 * @return Traversable|IRecord[]
		 */
		public function getIterator();
	}
