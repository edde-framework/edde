<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Query\Fragment\ITable;
		use Edde\Api\Query\Fragment\IWhereGroup;

		interface IUpdateQuery extends IInsertQuery {
			/**
			 * return schema fragment of this query
			 *
			 * @return ITable
			 */
			public function getTable(): ITable;

			/**
			 * has this update query where limitation
			 *
			 * @return bool
			 */
			public function hasWhere(): bool;

			/**
			 * update where
			 *
			 * @return IWhereGroup
			 */
			public function where(): IWhereGroup;
		}
