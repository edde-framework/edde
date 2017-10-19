<?php
	namespace Edde\Common\Query;

		use Edde\Api\Node\INode;

		/**
		 * Simple query class to hold IQL structura already built by somebody else.
		 */
		class Query extends AbstractQuery {
			/**
			 * @var INode
			 */
			protected $node;

			public function __construct(INode $node) {
				$this->node = $node;
			}

			/**
			 * @inheritdoc
			 */
			public function getQuery(): INode {
				return $this->node;
			}
		}
