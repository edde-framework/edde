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
			public function getName(): string {
				return $this->node->getAttribute('name');
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
			public function primary(bool $primary = true): IProperty {
				$this->node->setAttribute('primary', $primary);
				$this->required($primary);
				$this->unique($primary);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function isPrimary(): bool {
				return $this->node->getAttribute('primary', false);
			}

			/**
			 * @inheritdoc
			 */
			public function unique(bool $unique = true): IProperty {
				$this->node->setAttribute('unique', $unique);
				$this->required($unique);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function isUnique(): bool {
				return $this->node->getAttribute('unique', false);
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
			public function getGenerator(): ?string {
				return $this->node->getAttribute('generator');
			}

			/**
			 * @inheritdoc
			 */
			public function required(bool $required = true): IProperty {
				$this->node->setAttribute('required', $required);
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
