<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhereThan extends IWhereExpression {
			/**
			 * @param mixed $value
			 *
			 * @return IWhereGroup
			 */
			public function than($value): IWhereGroup;

			/**
			 * @param string $name
			 *
			 * @return IWhereGroup
			 */
			public function thanColumn(string $name): IWhereGroup;
		}
