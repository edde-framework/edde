<?php
	declare(strict_types=1);
	namespace Edde\Common\Query\Fragment;

		use Edde\Api\Node\INode;
		use Edde\Api\Query\Fragment\IFragment;
		use Edde\Common\Object\Object;

		abstract class AbstractFragment extends Object implements IFragment {
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

			/**
			 * @inheritdoc
			 */
			public function getNode(): INode {
				return $this->node;
			}
		}
