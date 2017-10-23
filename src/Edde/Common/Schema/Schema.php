<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
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
			protected $propertyList = null;

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
				$propertyList = $this->getPropertyList();
				if (isset($propertyList[$name]) === false) {
					throw new UnknownPropertyException(sprintf('Requested unknown property [%s] on schema [%s].', $name, $this->getName()));
				}
				return $propertyList[$name];
			}

			/**
			 * @inheritdoc
			 */
			public function getPropertyList(): array {
				if ($this->propertyList) {
					return $this->propertyList;
				}
				foreach ($this->node->getNodeList() as $node) {
					$this->propertyList[$node->getAttribute('name')] = new Property($node);
				}
				return $this->propertyList;
			}

			/**
			 * @inheritdoc
			 */
			public function getPrimaryList(): array {
				$propertyList = [];
				/** @var $property IProperty */
				foreach ($this->getPropertyList() as $name => $property) {
					if ($property->isPrimary()) {
						$propertyList[$name] = $property;
					}
				}
				return $propertyList;
			}

			/**
			 * @inheritdoc
			 */
			public function getNodeList(): array {
				return $this->node->getNodeList();
			}

			/**
			 * @inheritdoc
			 */
			public function property(string $name): IProperty {
				$this->propertyList = null;
				$this->node->addNode($node = new Node('property', null, ['name' => $name]));
				return new Property($node);
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
				return $this->property($name)->type('integer');
			}

			static public function create(string $name): ISchema {
				return new static(new Node('schema', null, ['name' => $name]));
			}
		}
