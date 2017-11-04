<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Query\Fragment\ITable;

		interface ISelectQuery extends IQuery {
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
			 * which source should be selected for the output (alias must exists)
			 *
			 * @param string $alias
			 *
			 * @return ISelectQuery
			 */
			public function select(string $alias): ISelectQuery;

			/**
			 * shorthand for where and ($name $relation $value); by default it takes last
			 * added alias
			 *
			 * @return ISelectQuery
			 */
			public function where(string $name, string $relation, $value): ISelectQuery;

			/**
			 * get base table of this query
			 *
			 * @return ITable
			 */
			public function getTable(): ITable;
		}
