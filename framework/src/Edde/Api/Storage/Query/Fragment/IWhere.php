<?php
	declare(strict_types=1);
	namespace Edde\Api\Storage\Query\Fragment;

	use Edde\Api\Storage\Query\IFragment;

	interface IWhere extends IFragment {
		/**
		 * where column is <expression> to value
		 *
		 * @param string $column
		 * @param string $operator
		 * @param mixed  $value
		 *
		 * @return IWhereGroup
		 */
		public function value(string $column, string $operator, $value): IWhereGroup;

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
