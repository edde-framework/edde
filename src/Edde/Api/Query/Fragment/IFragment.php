<?php
	declare(strict_types=1);
	namespace Edde\Api\Query\Fragment;

		use Edde\Api\Node\INode;

		interface IFragment {
			/**
			 * return an IQL node
			 *
			 * @return INode
			 */
			public function getNode(): INode;
		}
