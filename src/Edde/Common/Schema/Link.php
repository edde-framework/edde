<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\ILink;
		use Edde\Common\Object\Object;

		class Link extends Object implements ILink {
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
			public function getSchema(): string {
				return $this->node->getAttribute('schema');
			}

			/**
			 * @inheritdoc
			 */
			public function getTarget(): string {
				return $this->node->getAttribute('target');
			}

			/**
			 * @inheritdoc
			 */
			public function getSource(): string {
				return $this->node->getAttribute('source');
			}
		}
