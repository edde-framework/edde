<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhereFragment extends IFragment {
			/**
			 * where equals
			 *
			 * @param string      $name
			 * @param string|null $prefix column prefix
			 *
			 * @return IWhereTo
			 */
			public function eq(string $name, string $prefix = null): IWhereTo;

			/**
			 * where not equals
			 *
			 * @param string      $name
			 * @param string|null $prefix
			 *
			 * @return IWhereTo
			 */
			public function neq(string $name, string $prefix = null): IWhereTo;

			/**
			 * greater than
			 *
			 * @param string      $name
			 * @param string|null $prefix
			 *
			 * @return IWhereThan
			 */
			public function gt(string $name, string $prefix = null): IWhereThan;

			/**
			 * greater than equals
			 *
			 * @param string      $name
			 * @param string|null $prefix
			 *
			 * @return IWhereThan
			 */
			public function gte(string $name, string $prefix = null): IWhereThan;

			/**
			 * lesser than
			 *
			 * @param string      $name
			 * @param string|null $prefix
			 *
			 * @return IWhereThan
			 */
			public function lt(string $name, string $prefix = null): IWhereThan;

			/**
			 * lesser than equals
			 *
			 * @param string      $name
			 * @param string|null $prefix
			 *
			 * @return IWhereThan
			 */
			public function lte(string $name, string $prefix = null): IWhereThan;

			/**
			 * where in list of values
			 *
			 * @param string $name
			 *
			 * @return IWhereIn
			 */
			public function in(string $name): IWhereIn;

			/**
			 * group is like braces (sub where)
			 *
			 * @return IWhereFragment
			 */
			public function group(): IWhereFragment;

			/**
			 * where AND relation
			 *
			 * @return IWhereFragment
			 */
			public function and (): IWhereFragment;

			/**
			 * where or relation
			 *
			 * @return IWhereFragment
			 */
			public function or (): IWhereFragment;
		}
