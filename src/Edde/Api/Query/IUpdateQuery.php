<?php
	declare(strict_types=1);
	namespace Edde\Api\Query;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Fragment\IWhereFragment;

		interface IUpdateQuery extends IInsertQuery {
			/**
			 * update where
			 *
			 * @return IWhereFragment
			 */
			public function where(): IWhereFragment;

			/**
			 * has this update query where limitation
			 *
			 * @return bool
			 */
			public function hasWhere(): bool;

			/**
			 * return where IQL node
			 *
			 * @return INode
			 */
			public function getWhere(): INode;
		}
