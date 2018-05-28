<?php
	declare(strict_types=1);
	namespace Edde\Query;

	use stdClass;

	interface IQuery {
		/**
		 * add a source schema to the query
		 *
		 * @param string      $schema
		 * @param string|null $alias
		 *
		 * @return IQuery
		 */
		public function select(string $schema, string $alias = null): IQuery;

		/**
		 * make use of the given schema names; [$alias => $schema]
		 *
		 * @param string[] $schemas
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
		 * where equal to a value (not to an another property)
		 *
		 * @param string $alias    schema alias of a property
		 * @param string $property property of a source alias
		 * @param mixed  $value    simple scalar value
		 *
		 * @return IQuery
		 */
		public function equalTo(string $alias, string $property, $value): IQuery;

		/**
		 * are there some filters?
		 *
		 * @return bool
		 */
		public function hasWhere(): bool;

		/**
		 * return internal where objects
		 *
		 * @return stdClass[]
		 */
		public function getWheres(): array;

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
		 * @param string $alias
		 *
		 * @return string
		 *
		 * @throws QueryException
		 */
		public function getSelect(string $alias): string;

		/**
		 * return schemas used in this query; value could be duplicated as an array is [alias => value]
		 *
		 * @return string[]
		 */
		public function getSelects(): array;
	}
