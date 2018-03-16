<?php
	declare(strict_types=1);
	namespace Edde\Storage\Query\Fragment;

	use Edde\Storage\Query\IFragment;

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
		public function expression(string $column, string $expression, $value = null): IWhereGroup;

		/**
		 * return logical relation (and, or, ...)
		 *
		 * @return string
		 */
		public function getRelation(): string;

		/**
		 * return where data (column, operator, value or column, ...)
		 *
		 * @return array
		 */
		public function getWhere(): array;
	}
