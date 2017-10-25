<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\Exception\MultiplePrimaryException;
		use Edde\Api\Schema\Exception\NoPrimaryPropertyException;
		use Edde\Api\Schema\Exception\UnknownPropertyException;
		use Edde\Api\Schema\IProperty;
		use Edde\Api\Schema\ISchema;
		use Edde\Common\Node\Node;
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
			protected $linkList = null;
			protected $relationList = [];

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
			public function alias(string $alias): ISchema {
				$this->node->setAttribute('alias', $alias);
				return $this;
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
			public function getLinkList(): array {
				if ($this->linkList) {
					return $this->linkList;
				}
				$propertyList = [];
				foreach ($this->propertyList as $name => $property) {
					if ($property->isLink()) {
						$propertyList[$name] = $property;
					}
				}
				return $this->linkList = $propertyList;
			}

			/**
			 * @inheritdoc
			 */
			public function getRelationList(string $target): array {
				if (isset($this->relationList[$target])) {
					return $this->relationList[$target];
				}
				$linkList = [];
				/** @var $node INode */
				foreach ($this->node->getNode('link-list')->getNodeList() as $node) {
					if ($node->getAttribute('schema') === $target) {
						$linkList[] = $this->getProperty($node->getAttribute('source'));
					}
				}
				return $this->relationList[$target] = $linkList;
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
			public function property(string $name): IProperty {
				$this->node->getNode('property-list')->addNode($node = new Node('property', null, ['name' => $name]));
				return $this->propertyList[$name] = new Property($this->node, $node);
			}

			/**
			 * @inheritdoc
			 */
			public function primary(string $name): IProperty {
				return $this->property($name)->primary();
			}

			/**
			 * @inheritdoc
			 */
			public function string(string $name): IProperty {
				return $this->property($name)->type('string');
			}

			/**
			 * @inheritdoc
			 */
			public function text(string $name): IProperty {
				return $this->property($name)->type('text');
			}

			/**
			 * @inheritdoc
			 */
			public function integer(string $name): IProperty {
				return $this->property($name)->type('int');
			}

			static public function create(string $name): ISchema {
				return new static(new Node('schema', null, ['name' => $name]));
			}
		}
