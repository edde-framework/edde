<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhere extends IFragment {
			/**
			 * where column is <expression> to value
			 *
			 * @param string $column
			 * @param string $type
			 * @param mixed  $value
			 *
			 * @return IWhereGroup
			 */
			public function value(string $column, string $type, $value): IWhereGroup;

			/**
			 * return logical relation (and, or, ...)
			 *
			 * @return string
			 */
			public function getRelation(): string;
		}
