<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhere extends IFragment {
			/**
			 * where column is <expression> to value
			 *
			 * @param string $column
			 * @param string $expression
			 * @param mixed  $value
			 *
			 * @return IWhereGroup
			 */
			public function value(string $column, string $expression, $value): IWhereGroup;

			/**
			 * return logical relation (and, or, ...)
			 *
			 * @return string
			 */
			public function getRelation(): string;

			/**
			 * @return IWhereExpression
			 */
			public function getExpression(): IWhereExpression;
		}
