<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Query\Fragment\IWhereFragment;

		interface IUpdateQuery extends IInsertQuery {
			/**
			 * has this update query where limitation
			 *
			 * @return bool
			 */
			public function hasWhere(): bool;

			/**
			 * update where
			 *
			 * @return IWhereFragment
			 */
			public function where(): IWhereFragment;
		}
