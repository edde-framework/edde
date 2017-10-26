<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\ILink;
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
			public function isPrimary(): bool {
				return $this->node->getAttribute('primary', false);
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
			public function getGenerator(): ?string {
				return $this->node->getAttribute('generator');
			}

			/**
			 * @inheritdoc
			 */
			public function getFilter(): ?string {
				return $this->node->getAttribute('filter');
			}

			/**
			 * @inheritdoc
			 */
			public function getSanitizer(): ?string {
				return $this->node->getAttribute('sanitizer');
			}

			/**
			 * @inheritdoc
			 */
			public function getType(): string {
				return $this->node->getAttribute('type', 'string');
			}

			/**
			 * @inheritdoc
			 */
			public function isLink(): bool {
				return $this->link !== null;
			}

			/**
			 * @inheritdoc
			 */
			public function getLink(): ILink {
				return new Link($this->link);
			}
		}
