<?php
	namespace Edde\Api\Query\Fragment;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\IQuery;

		interface IFragment {
			/**
			 * return an IQL node
			 *
			 * @return INode
			 */
			public function getNode(): INode;

			/**
			 * return or create a root Query using root IQL node
			 *
			 * @return IQuery
			 */
			public function query(): IQuery;
		}
