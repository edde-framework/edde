<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Filter\FilterException;
	use Edde\Query\IQuery;
	use Edde\Query\QueryException;
	use Edde\Schema\SchemaException;
	use Edde\Storage\IRecord;
	use Edde\Storage\StorageException;
	use Generator;

	/**
	 * A collection is read-only result of some (usually selection) query.
	 */
	interface ICollection {
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
		 *
		 * @throws SchemaException
		 */
		public function select(string $schema, string $alias = null): ICollection;

		/**
		 * @param string[] $schemas
		 *
		 * @return ICollection
		 *
		 * @throws SchemaException
		 */
		public function selects(array $schemas): ICollection;

		/**
		 * @param string $attach
		 * @param string $to
		 * @param string $relation
		 *
		 * @return ICollection
		 */
		public function attach(string $attach, string $to, string $relation): ICollection;

		/**
		 * @param string $alias
		 * @param string $property
		 * @param string $order
		 *
		 * @return ICollection
		 */
		public function order(string $alias, string $property, string $order = 'asc'): ICollection;

		/**
		 * limit/offset
		 *
		 * @param int $page
		 * @param int $size
		 *
		 * @return ICollection
		 */
		public function page(int $page, int $size): ICollection;

		/**
		 * return count of the given alias
		 *
		 * @param string $alias
		 *
		 * @return int
		 *
		 * @throws QueryException
		 * @throws StorageException
		 * @throws SchemaException
		 */
		public function count(string $alias): int;

		/**
		 * @param array $binds
		 *
		 * @return Generator|IRecord[]
		 *
		 * @throws StorageException
		 * @throws FilterException
		 * @throws QueryException
		 * @throws SchemaException
		 */
		public function execute(array $binds = []): Generator;

		/**
		 * get exactly one entity
		 *
		 * @param string $alias
		 * @param array  $binds
		 *
		 * @return IEntity
		 *
		 * @throws StorageException
		 * @throws EntityNotFoundException
		 */
		public function getEntity(string $alias, array $binds = []): IEntity;
	}
