<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\IPropertyLink;
		use Edde\Common\Object\Object;

		class PropertyLink extends Object implements IPropertyLink {
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
