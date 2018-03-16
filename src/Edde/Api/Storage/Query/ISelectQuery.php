<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage\Query;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\Query\Fragment\IJoin;
	use Edde\Api\Storage\Query\Fragment\IWhereGroup;
	use Edde\Exception\Storage\UnknownAliasException;

	interface ISelectQuery extends IQuery {
		/**
		 * @param string $alias
		 * @param string $schema
		 *
		 * @return ISelectQuery
		 */
		public function link(string $alias, string $schema): ISelectQuery;

		/**
		 * join the given schema the previously joined schema
		 *
		 * @param string      $alias
		 * @param string      $schema
		 * @param string|null $relation
		 *
		 * @return ISelectQuery
		 */
		public function join(string $alias, string $schema, string $relation = null): ISelectQuery;

		/**
		 * @return IJoin[]
		 */
		public function getJoins(): array;

		/**
		 * @return ISchema[]
		 */
		public function getSchemas(): array;

		/**
		 * shorthand for where and ($name $relation $value); by default it takes last
		 * added alias
		 *
		 * @param string $name
		 * @param string $expression
		 * @param mixed  $value
		 *
		 * @return ISelectQuery
		 */
		public function where(string $name, string $expression, $value = null): ISelectQuery;

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
		 * which alias should be counted
		 *
		 * @param string $alias
		 *
		 * @return ISelectQuery
		 */
		public function count(string $alias = null): ISelectQuery;

		/**
		 * @return bool
		 */
		public function isCount(): bool;

		/**
		 * @return string
		 */
		public function getCount(): string;

		/**
		 * this method could be called many times; it marks data which should be returned in Collection's Record
		 *
		 * @param string  $alias
		 * @param ISchema $schema
		 *
		 * @return ISelectQuery
		 */
		public function alias(string $alias, ISchema $schema): ISelectQuery;

		/**
		 * return source alias
		 *
		 * @return string
		 */
		public function getAlias(): string;

		/**
		 * get schema for the given alias
		 *
		 * @param string $alias
		 *
		 * @return ISchema
		 * @throws UnknownAliasException
		 */
		public function getSchema(string $alias = null): ISchema;
	}
