<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		interface IWhereTo extends IWhereExpression {
			/**
			 * where to scalar value, returns a relation
			 *
			 * @param mixed $value
			 *
			 * @return IWhereGroup
			 */
			public function to($value): IWhereGroup;

			/**
			 * where to a column name
			 *
			 * @param string $name
			 *
			 * @return IWhereGroup
			 */
			public function toColumn(string $name): IWhereGroup;

			/**
			 * return schema fragment this where belongs to
			 *
			 * @return ISchemaFragment
			 */
			public function getSchemaFragment(): ISchemaFragment;
		}
