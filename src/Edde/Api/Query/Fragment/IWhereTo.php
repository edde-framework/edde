<?php
	namespace Edde\Api\Query\Fragment;

		interface IWhereTo extends IFragment {
			/**
			 * where to scalar value, returns a relation
			 *
			 * @param mixed $value
			 *
			 * @return IWhereRelation
			 */
			public function to($value): IWhereRelation;

			/**
			 * where to a column namel
			 *
			 * @param string $name
			 *
			 * @return IWhereRelation
			 */
			public function toColumn(string $name): IWhereRelation;
		}
