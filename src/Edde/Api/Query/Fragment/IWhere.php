<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhere extends IFragment {
			/**
			 * where equals
			 *
			 * @param string      $name
			 * @param string|null $alias
			 *
			 * @return IWhereTo
			 */
			public function eq(string $name, string $alias = null): IWhereTo;

			/**
			 * greater than
			 *
			 * @param string $name
			 *
			 * @return IWhereThan
			 */
			public function gt(string $name): IWhereThan;

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
