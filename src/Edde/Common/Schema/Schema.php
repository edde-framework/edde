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
			public function property(string $name): IProperty {
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

			static public function create(string $name): ISchema {
				return new static(new Node($name));
			}
		}
