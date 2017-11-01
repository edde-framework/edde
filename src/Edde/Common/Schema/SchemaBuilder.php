<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

		use Edde\Api\Node\INode;
		use Edde\Api\Schema\IPropertyBuilder;
		use Edde\Api\Schema\ISchema;
		use Edde\Api\Schema\ISchemaBuilder;
		use Edde\Common\Node\Node;
		use Edde\Common\Object\Object;

		class SchemaBuilder extends Object implements ISchemaBuilder {
			/**
			 * @var INode
			 */
			protected $node;
			/**
			 * @var IPropertyBuilder[]
			 */
			protected $propertyBuilderList = [];
			/**
			 * @var ISchema
			 */
			protected $schema;

			public function __construct(string $name) {
				$this->node = new Node('schema', null, ['name' => $name]);
			}

			/**
			 * @inheritdoc
			 */
			public function alias(string $alias): ISchemaBuilder {
				$this->node->setAttribute('alias', $alias);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function relation(bool $relation): ISchemaBuilder {
				$this->node->setAttribute('is-relation', $relation);
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function property(string $name): IPropertyBuilder {
				$this->node->getNode('property-list')->addNode($node = new Node('property', null, ['name' => $name]));
				return $this->propertyBuilderList[$name] = new PropertyBuilder($this->node, $node);
			}

			/**
			 * @inheritdoc
			 */
			public function primary(string $name): IPropertyBuilder {
				return $this->property($name)->primary();
			}

			/**
			 * @inheritdoc
			 */
			public function string(string $name): IPropertyBuilder {
				return $this->property($name)->type('string');
			}

			/**
			 * @inheritdoc
			 */
			public function text(string $name): IPropertyBuilder {
				return $this->property($name)->type('text');
			}

			/**
			 * @inheritdoc
			 */
			public function integer(string $name): IPropertyBuilder {
				return $this->property($name)->type('int');
			}

			/**
			 * @inheritdoc
			 */
			public function getSchema(): ISchema {
				if ($this->schema) {
					return $this->schema;
				}
				$propertyList = [];
				foreach ($this->propertyBuilderList as $name => $propertyBuilder) {
					$propertyList[$name] = $propertyBuilder->getProperty();
				}
				return $this->schema = new Schema($this->node, $propertyList);
			}
		}
