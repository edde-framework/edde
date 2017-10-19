<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Node\Node;
		use Edde\Common\Object\Object;

		class Schema extends Object implements ISchema {
			/**
			 * @var INode
			 */
			protected $node;

			public function __construct(INode $node = null) {
				$this->node = $node ?: new Node();
			}

			/**
			 * @inheritdoc
			 */
			public function getName(): string {
				return $this->node->getName();
			}
		}
