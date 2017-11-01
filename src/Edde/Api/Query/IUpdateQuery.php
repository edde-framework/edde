<?php
	namespace Edde\Api\Query;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Fragment\IWhere;

		interface IUpdateQuery extends IInsertQuery {
			/**
			 * update where
			 *
			 * @return IWhere
			 */
			public function where(): IWhere;

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
