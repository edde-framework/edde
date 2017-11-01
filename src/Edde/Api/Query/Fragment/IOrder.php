<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IOrder extends IFragment {
			/**
			 * ascending by the column
			 *
			 * @param string $column
			 *
			 * @return IOrder
			 */
			public function asc(string $column): IOrder;

			/**
			 * descending by the given column
			 *
			 * @param string $column
			 *
			 * @return IOrder
			 */
			public function desc(string $column): IOrder;
		}
