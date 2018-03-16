<?php
	declare(strict_types=1);
	namespace Edde\Api\Entity;

	use Edde\Api\Storage\Query\ISelectQuery;
	use Edde\Entity\EntityException;
	use Edde\Entity\EntityNotFoundException;
	use Edde\Schema\ISchema;
	use Edde\Schema\SchemaException;
	use IteratorAggregate;
	use Traversable;

	/**
	 * A collection is read-only result of some (usually selection) query.
	 */
	interface ICollection extends IteratorAggregate {
		/**
		 * when an alias is set with a schema, it's returned in Collection's Record; if nothing
		 * is set, nothing is returned (collection is not executed)
		 *
		 * @param string  $alias
		 * @param ISchema $schema
		 *
		 * @return ICollection
		 */
		public function schema(string $alias, ISchema $schema): ICollection;

		/**
		 * @param string $alias
		 *
		 * @return ISchema
		 *
		 * @throws EntityException
		 */
		public function getSchema(string $alias): ISchema;

		/**
		 * set custom query for this collection
		 *
		 * @param ISelectQuery $query
		 *
		 * @return ICollection
		 */
		public function query(ISelectQuery $query): ICollection;

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
		 * @param string $alias
		 *
		 * @return IEntity
		 *
		 * @throws EntityException
		 * @throws EntityNotFoundException
		 */
		public function getEntity(string $alias): IEntity;

		/**
		 * get exacly one record or throw an exception if result is empty
		 *
		 * @return IRecord
		 *
		 * @throws EntityException
		 */
		public function getRecord(): IRecord;

		/**
		 * a bit magical method which try to find an entity by primary key and all
		 * unique keys
		 *
		 * @param string $alias
		 * @param mixed  $name
		 *
		 * @return IEntity
		 *
		 * @throws EntityNotFoundException
		 */
		public function entity(string $alias, $name): IEntity;

		/**
		 * join the given target schema to the current one
		 *
		 * @param string $source source alias
		 * @param string $target
		 * @param string $alias
		 * @param array  $on
		 * @param string $relation
		 *
		 * @return ICollection
		 */
		public function join(string $source, string $target, string $alias, array $on = null, string $relation = null): ICollection;

		/**
		 * @param string      $source
		 * @param string      $target
		 * @param string      $alias
		 * @param array|null  $on
		 * @param string|null $relation
		 *
		 * @return ICollection
		 */
		public function reverseJoin(string $source, string $target, string $alias, array $on = null, string $relation = null): ICollection;

		/**
		 * simple and where
		 *
		 * @param string $name
		 * @param string $expression
		 * @param mixed  $value
		 *
		 * @return ICollection
		 */
		public function where(string $name, string $expression, $value = null): ICollection;

		/**
		 * @param string $name
		 * @param bool   $asc
		 *
		 * @return ICollection
		 */
		public function order(string $name, bool $asc = true): ICollection;

		/**
		 * @param string $name
		 *
		 * @return ICollection
		 */
		public function orderAsc(string $name): ICollection;

		/**
		 * @param string $name
		 *
		 * @return ICollection
		 */
		public function orderDesc(string $name): ICollection;

		/**
		 * @param int $limit
		 * @param int $page
		 *
		 * @return ICollection
		 */
		public function limit(int $limit, int $page): ICollection;

		/**
		 * link the given schema
		 *
		 * @return ICollection
		 *
		 * @throws SchemaException
		 */
		public function link(string $alias, string $schema): ICollection;

		/**
		 * return count of items in current collection setup
		 *
		 * @param string|null $alias
		 *
		 * @return int
		 */
		public function count(string $alias): int;

		/**
		 * @return Traversable|IRecord[]
		 */
		public function getIterator();
	}
