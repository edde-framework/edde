<?php
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
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
				return $this->node->getName();
			}

			/**
			 * @inheritdoc
			 */
			public function getPropertyList(): array {
				if ($this->propertyList) {
					return $this->propertyList;
				}
				foreach ($this->node->getNodeList() as $node) {
					$this->propertyList[] = new Property($node);
				}
				return $this->propertyList;
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
				$this->node->addNode($node = new Node($name));
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
				return new static(new Node($name));
			}
		}
