<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhereGroup;

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
			 * preferred way, how to configure details of the query (for example
			 * filter based on properties in joined tables)
			 *
			 * @return IWhereGroup
			 */
			public function where(): IWhereGroup;

			/**
			 * get base table of this query
			 *
			 * @return ITable
			 */
			public function getTable(): ITable;
		}
