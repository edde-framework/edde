<?php
	namespace Edde\Api\Query\Fragment;

		interface IWhereThan extends IFragment {
			/**
			 * where than parameter
			 *
			 * @param mixed $value
			 *
			 * @return IWhereRelation
			 */
			public function than($value): IWhereRelation;

			/**
			 * where than column
			 *
			 * @param string $name
			 *
			 * @return IWhereRelation
			 */
			public function thanColumn(string $name): IWhereRelation;
		}
