<?php
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\IQuery;
		use Edde\Common\Object\Object;
		use Edde\Common\Query\Query;

		abstract class AbstractFragment extends Object {
			/**
			 * @var INode
			 */
			protected $root;
			/**
			 * @var INode
			 */
			protected $node;

			/**
			 * @param INode $root
			 * @param INode $node
			 */
			public function __construct(INode $root, INode $node = null) {
				$this->root = $root;
				$this->node = $node;
			}

			public function getNode() {
				return $this->node;
			}

			public function query(): IQuery {
				return new Query($this->root);
			}
		}
