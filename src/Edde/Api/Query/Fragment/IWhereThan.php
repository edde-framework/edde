<?php
	declare(strict_types=1);
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
			 * @param string      $name
			 * @param string|null $prefix
			 *
			 * @return IWhereRelation
			 */
			public function thanColumn(string $name, string $prefix = null): IWhereRelation;
		}
