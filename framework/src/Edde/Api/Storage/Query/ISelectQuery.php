<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage\Query;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\Exception\QueryException;
	use Edde\Api\Storage\Query\Fragment\IJoin;
	use Edde\Api\Storage\Query\Fragment\IWhereGroup;

	interface ISelectQuery extends IQuery {
		/**
		 * @return ISchema
		 */
		public function getSchema(): ISchema;

		/**
		 * @return string
		 */
		public function getAlias(): string;

		/**
		 * @param string $schema
		 * @param string $alias
		 *
		 * @return ISelectQuery
		 */
		public function link(string $schema, string $alias): ISelectQuery;

		/**
		 * join the given schema the previously joined schema
		 *
		 * @param string $schema
		 * @param string $alias
		 *
		 * @return ISelectQuery
		 */
		public function join(string $schema, string $alias): ISelectQuery;

		/**
		 * @return IJoin[]
		 */
		public function getJoins(): array;

		/**
		 * @return string
		 */
		public function getReturn(): string;

		/**
		 * shorthand for where and ($name $relation $value); by default it takes last
		 * added alias
		 *
		 * @param string $name
		 * @param string $relation
		 * @param mixed  $value
		 *
		 * @return ISelectQuery
		 */
		public function where(string $name, string $relation, $value): ISelectQuery;

		/**
		 * @return bool
		 */
		public function hasWhere(): bool;

		/**
		 * @return IWhereGroup
		 */
		public function getWhere(): IWhereGroup;

		/**
		 * @param string $name
		 * @param bool   $asc
		 *
		 * @return ISelectQuery
		 */
		public function order(string $name, bool $asc = true): ISelectQuery;

		/**
		 * @return bool
		 */
		public function hasOrder(): bool;

		/**
		 * @return string[]
		 */
		public function getOrders(): array;

		/**
		 * setup a page of records to be returned; this should be used in common to prevent getting
		 * huge pieces of data
		 *
		 * @param int $limit number of items per page
		 * @param int $page  current page (0, 1, ...)
		 *
		 * @return ISelectQuery
		 */
		public function limit(int $limit, int $page): ISelectQuery;

		/**
		 * is the limit set?
		 *
		 * @return bool
		 */
		public function hasLimit(): bool;

		/**
		 * return current limit; first value is limit, second page
		 *
		 * @return array
		 */
		public function getLimit(): array;

		/**
		 * setup query to get item count
		 *
		 * @param bool $count
		 *
		 * @return ISelectQuery
		 */
		public function count(bool $count = true): ISelectQuery;

		/**
		 * @return bool
		 */
		public function isCount(): bool;

		/**
		 * which source should be returned (selected) for the output (alias must exists)
		 *
		 * @param string $alias
		 *
		 * @return ISelectQuery
		 * @throws QueryException
		 */
		public function return(string $alias = null): ISelectQuery;
	}
