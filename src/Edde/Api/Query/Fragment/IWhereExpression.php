<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhereExpression extends IFragment {
			/**
			 * get source column name
			 *
			 * @return string
			 */
			public function getName(): string;

			/**
			 * get target of an expression (column, parameter, ...)
			 *
			 * @return string
			 */
			public function getTarget(): string;

			/**
			 * value could be target colume name or... just value
			 *
			 * @return mixed
			 */
			public function getValue();
		}
