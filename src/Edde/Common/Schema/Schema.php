<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\Exception\MultiplePrimaryException;
		use Edde\Api\Schema\Exception\NoPrimaryPropertyException;
		use Edde\Api\Schema\Exception\UnknownPropertyException;
		use Edde\Api\Schema\ILink;
		use Edde\Api\Schema\IProperty;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Object\Object;

		class Schema extends Object implements ISchema {
			/**
			 * @var INode
			 */
			protected $node;
			/**
			 * @var IProperty[]
			 */
			protected $propertyList = [];
			protected $primaryList = null;
			protected $uniqueList = null;
			protected $linkToList = [];
			protected $linkList = [];

			public function __construct(INode $node, array $propertyList) {
				$this->node = $node;
				$this->propertyList = $propertyList;
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
			public function getNodeList(): array {
				return $this->node->getNode('property-list')->getNodeList();
			}

			/**
			 * @inheritdoc
			 */
			public function isRelation(): bool {
				return $this->node->getAttribute('is-relation', false);
			}

			/**
			 * @inheritdoc
			 */
			public function hasAlias(): bool {
				return $this->node->hasAttribute('alias');
			}

			/**
			 * @inheritdoc
			 */
			public function getAlias(): ?string {
				return $this->node->getAttribute('alias');
			}

			/**
			 * @inheritdoc
			 */
			public function getProperty(string $name): IProperty {
				if (isset($this->propertyList[$name]) === false) {
					throw new UnknownPropertyException(sprintf('Requested unknown property [%s] on schema [%s].', $name, $this->getName()));
				}
				return $this->propertyList[$name];
			}

			/**
			 * @inheritdoc
			 */
			public function getPropertyList(): array {
				return $this->propertyList;
			}

			/**
			 * @inheritdoc
			 */
			public function getPrimaryList(): array {
				if ($this->primaryList) {
					return $this->primaryList;
				}
				$propertyList = [];
				foreach ($this->propertyList as $name => $property) {
					if ($property->isPrimary()) {
						$propertyList[$name] = $property;
					}
				}
				return $this->primaryList = $propertyList;
			}

			/**
			 * @inheritdoc
			 */
			public function getPrimary(): IProperty {
				if (empty($primaryList = $this->getPrimaryList())) {
					throw new NoPrimaryPropertyException(sprintf('Schema [%s] has no primary properties.', $this->getName()));
				} else if (count($primaryList) > 1) {
					throw new MultiplePrimaryException(sprintf('Schema [%s] has more primary properties [%s].', $this->getName(), implode(', ', array_keys($primaryList))));
				}
				return reset($primaryList);
			}

			/**
			 * @inheritdoc
			 */
			public function getUniqueList(): array {
				if ($this->uniqueList) {
					return $this->uniqueList;
				}
				$this->uniqueList = [];
				foreach ($this->propertyList as $name => $property) {
					if ($property->isUnique()) {
						$this->uniqueList[$name] = $property;
					}
				}
				return $this->uniqueList;
			}

			/**
			 * @inheritdoc
			 */
			public function linkTo(ILink $link): ISchema {
				$this->linkToList[$link->getSourceSchema()->getName()][$link->getSourceProperty()->getName()] = $link;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getLinkToList(string $schema): array {
				return $this->linkToList[$schema] ?? [];
			}

			/**
			 * @inheritdoc
			 */
			public function link(ILink $link): ISchema {
				$this->linkList[$link->getTargetSchema()->getName()][$link->getTargetProperty()->getName()] = $link;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function getLinkList(string $schema): array {
				return $this->linkList[$schema] ?? [];
			}
		}
