<?php
	namespace Edde\Api\Query;

		use Edde\Api\Query\Fragment\IOrder;
		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhere;

		/**
		 * Formal interface for Select Queries (also to map relevant fragments).
		 */
		interface ISelectQuery extends IQuery {
			/**
			 * add this table as source (usually FROM)
			 *
			 * @param string      $name
			 * @param string|null $alias
			 *
			 * @return ITable
			 */
			public function table(string $name, string $alias = null): ITable;

			/**
			 * do subselect in column list
			 *
			 * @param ISelectQuery $selectQuery
			 * @param string       $alias
			 *
			 * @return ISelectQuery
			 */
			public function select(ISelectQuery $selectQuery, string $alias): ISelectQuery;

			/**
			 * where clausule
			 *
			 * @return IWhere
			 */
			public function where(): IWhere;

			/**
			 * order by
			 *
			 * @return IOrder
			 */
			public function order(): IOrder;
		}