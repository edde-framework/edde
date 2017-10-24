<?php
	namespace Edde\Api\Query\Fragment;

		interface IWhere extends IFragment {
			/**
			 * where equals
			 *
			 * @param string $name
			 *
			 * @return IWhereTo
			 */
			public function eq(string $name): IWhereTo;

			/**
			 * where not equals
			 *
			 * @param string $name
			 *
			 * @return IWhereTo
			 */
			public function neq(string $name): IWhereTo;

			/**
			 * greater than
			 *
			 * @param string $name
			 *
			 * @return IWhereThan
			 */
			public function gt(string $name): IWhereThan;

			/**
			 * greater than equals
			 *
			 * @param string $name
			 *
			 * @return IWhereThan
			 */
			public function gte(string $name): IWhereThan;

			/**
			 * lesser than
			 *
			 * @param string $name
			 *
			 * @return IWhereThan
			 */
			public function lt(string $name): IWhereThan;

			/**
			 * lesser than equals
			 *
			 * @param string $name
			 *
			 * @return IWhereThan
			 */
			public function lte(string $name): IWhereThan;

			/**
			 * where in list of values
			 *
			 * @param string $name
			 *
			 * @return IWhereIn
			 */
			public function in(string $name): IWhereIn;

			/**
			 * group is like braces (subwhere)
			 *
			 * @return IWhere
			 */
			public function group(): IWhere;

			/**
			 * where AND relation
			 *
			 * @return IWhere
			 */
			public function and (): IWhere;

			/**
			 * where or relation
			 *
			 * @return IWhere
			 */
			public function or (): IWhere;
		}
