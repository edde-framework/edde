<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use Edde\Schema\ISchema;
	use stdClass;

	/**
	 * Low level query support.
	 */
	interface IQuery {
		/**
		 * add a source schema to the query
		 *
		 * @param ISchema     $schema
		 * @param string|null $alias
		 *
		 * @return IQuery
		 */
		public function select(ISchema $schema, string $alias = null): IQuery;

		/**
		 * make use of the given schema names; [$alias => $schema]
		 *
		 * @param ISchema[] $schemas
		 *
		 * @return IQuery
		 */
		public function selects(array $schemas): IQuery;

		/**
		 * attach is kind of "join" - it makes relation between schema aliases
		 *
		 * @param string $attach   source alias (who is being attached)
		 * @param string $to       target alias (target of an attach)
		 * @param string $relation alias used as a relation
		 *
		 * @return IQuery
		 */
		public function attach(string $attach, string $to, string $relation): IQuery;

		/**
		 * @return bool
		 */
		public function hasAttaches(): bool;

		/**
		 * is the given alias attached?
		 *
		 * @param string $alias
		 *
		 * @return bool
		 */
		public function isAttached(string $alias): bool;

		/**
		 * return all attaches in this query
		 *
		 * @return stdClass[]
		 */
		public function getAttaches(): array;

		/**
		 * @return IWheres
		 */
		public function wheres(): IWheres;

		/**
		 * @param string $alias
		 * @param string $property
		 * @param string $order
		 *
		 * @return IQuery
		 */
		public function order(string $alias, string $property, string $order = 'asc'): IQuery;

		/**
		 * should this query be ordered?
		 *
		 * @return bool
		 */
		public function hasOrder(): bool;

		/**
		 * @return stdClass[]
		 */
		public function getOrders(): array;

		/**
		 * @param int $page
		 * @param int $size
		 *
		 * @return IQuery
		 */
		public function page(int $page, int $size): IQuery;

		/**
		 * @return bool
		 */
		public function hasPage(): bool;

		/**
		 * @return stdClass
		 */
		public function getPage(): stdClass;

		/**
		 * which alias should be returned by the query
		 *
		 * @param string $alias
		 *
		 * @return IQuery
		 */
		public function return(string $alias): IQuery;

		/**
		 * @param array $aliases
		 *
		 * @return IQuery
		 */
		public function returns(array $aliases): IQuery;

		/**
		 * pickup just the given "column" from a query; the result will be just that property
		 *
		 * @param string      $alias
		 * @param string      $property
		 * @param string|null $name
		 *
		 * @return IQuery
		 */
		public function just(string $alias, string $property, string $name = null): IQuery;

		/**
		 * @return string[]
		 */
		public function getReturns(): array;

		/**
		 * @param string $alias
		 *
		 * @return ISchema
		 *
		 * @throws QueryException
		 */
		public function getSchema(string $alias): ISchema;

		/**
		 * return schemas used in this query; value could be duplicated as an array is [alias => value]
		 *
		 * @return ISchema[]
		 */
		public function getSelects(): array;

		/**
		 * return unique list of used schemas; $schema => $schema (both strings)
		 *
		 * @return ISchema[]
		 */
		public function getSchemas(): array;

		/**
		 * return parameter definitions (this is not for actual parameters)
		 *
		 * @return IParams
		 */
		public function getParams(): IParams;

		/**
		 * return parameters with bound values
		 *
		 * @param array $bind
		 *
		 * @return IParam[]
		 *
		 * @throws QueryException
		 */
		public function params(array $bind): array;

		/**
		 * mark this query as a count query
		 *
		 * @param bool $count
		 *
		 * @return IQuery
		 */
		public function count(bool $count = true): IQuery;

		/**
		 * is this query "count" query?
		 *
		 * @return bool
		 */
		public function isCount(): bool;
	}
