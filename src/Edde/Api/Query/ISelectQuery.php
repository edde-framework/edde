<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Query\Fragment\IJoin;
		use Edde\Api\Query\Fragment\IWhereGroup;
		use Edde\Api\Schema\ISchema;

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
			 * which source should be returned (selected) for the output (alias must exists)
			 *
			 * @param string $alias
			 *
			 * @return ISelectQuery
			 */
			public function return(string $alias = null): ISelectQuery;
		}
