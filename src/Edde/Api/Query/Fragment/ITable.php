<?php
	namespace Edde\Api\Query\Fragment;

		interface ITable extends IFragment {
			/**
			 * select all on this table
			 *
			 * @return ITable
			 */
			public function all(): ITable;

			/**
			 * select the given column from the table
			 *
			 * @param string      $name
			 * @param string|null $alias
			 *
			 * @return ITable
			 */
			public function column(string $name, string $alias = null): ITable;

			/**
			 * attach another table to select
			 *
			 * @param string      $name
			 * @param string|null $alias
			 *
			 * @return ITable
			 */
			public function table(string $name, string $alias = null): ITable;
		}
