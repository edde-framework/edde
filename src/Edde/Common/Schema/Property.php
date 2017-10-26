<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\Exception\LinkException;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\IProperty;
		use Edde\Common\Object\Object;

		class Property extends Object implements IProperty {
			/**
			 * @var INode
			 */
			protected $root;
			/**
			 * @var INode
			 */
			protected $node;
			/**
			 * @var ILink
			 */
			protected $link;

			public function __construct(INode $root, INode $node) {
				$this->root = $root;
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
				return $this->node->hasNode('link');
			}

			/**
			 * @inheritdoc
			 */
			public function getLink(): ILink {
				if ($this->link) {
					return $this->link;
				} else if ($this->isLink() === false) {
					throw new LinkException(sprintf('Property [%s::%s] is not a link.', $this->root->getAttribute('name'), $this->getName()));
				}
				return $this->link = new Link($this->node->getNode('link'));
			}
		}
