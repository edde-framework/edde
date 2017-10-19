<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\IProperty;
		use Edde\Common\Object\Object;

		class Property extends Object implements IProperty {
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
			public function getNode(): INode {
				return $this->node;
			}

			/**
			 * @inheritdoc
			 */
			public function primary(): IProperty {
				$this->node->setAttribute('primary', true);
				$this->required();
				$this->unique();
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function unique(): IProperty {
				$this->node->setAttribute('unique', true);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function generator(string $string): IProperty {
				$this->node->setAttribute('generator', $string);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function required(): IProperty {
				$this->node->setAttribute('required', true);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function type(string $type): IProperty {
				$this->node->setAttribute('type', $type);
				return $this;
			}
		}
