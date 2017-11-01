<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhereFragment extends IFragment {
			/**
			 * where equals
			 *
			 * @param string $name
			 *
			 * @return IWhereTo
			 */
			public function eq(string $name): IWhereTo;

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
