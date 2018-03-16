<?php
	declare(strict_types=1);
	namespace Edde\Common\Schema;

	use Edde\Api\Node\INode;
	use Edde\Common\Node\Node;
	use Edde\Object;
	use Edde\Schema\ILinkBuilder;
	use Edde\Schema\IPropertyBuilder;
	use Edde\Schema\ISchema;
	use Edde\Schema\ISchemaBuilder;
	use Edde\Schema\Schema;
	use Edde\Schema\SchemaException;

	class SchemaBuilder extends Object implements ISchemaBuilder {
		/** @var INode */
		protected $node;
		/** @var IPropertyBuilder[] */
		protected $propertyBuilders = [];
		/** @var \Edde\Schema\ISchema */
		protected $schema;
		/** @var ILinkBuilder[] */
		protected $linkBuilders = [];

		public function __construct(string $name) {
			$this->node = new Node('schema', ['name' => $name], null);
		}

		/** @inheritdoc */
		public function alias(string $alias): ISchemaBuilder {
			$this->node->setAttribute('alias', $alias);
			return $this;
		}

		/** @inheritdoc */
		public function relation(bool $relation): \Edde\Schema\ISchemaBuilder {
			$this->node->setAttribute('is-relation', $relation);
			return $this;
		}

		/** @inheritdoc */
		public function property(string $name): IPropertyBuilder {
			$this->node->getNode('properties')->add($node = new Node('property', ['name' => $name]));
			return $this->propertyBuilders[$name] = new PropertyBuilder($this->node, $node);
		}

		/** @inheritdoc */
		public function primary(string $name): IPropertyBuilder {
			return $this->property($name)->primary();
		}

		/** @inheritdoc */
		public function string(string $name): IPropertyBuilder {
			return $this->property($name)->type('string');
		}

		/** @inheritdoc */
		public function text(string $name): IPropertyBuilder {
			return $this->property($name)->type('text');
		}

		/** @inheritdoc */
		public function integer(string $name): IPropertyBuilder {
			return $this->property($name)->type('int');
		}

		/** @inheritdoc */
		public function link(ILinkBuilder $linkBuilder): ISchemaBuilder {
			$this->linkBuilders[] = $linkBuilder;
			if ($this->node->getAttribute('is-relation', false) && count($this->linkBuilders) > 2) {
				throw new SchemaException(sprintf('Relation schema [%s] must have exactly two links; if you need more links, remove "relation" flag from the schema.', $this->node->getAttribute('name')));
			}
			return $this;
		}

		/** @inheritdoc */
		public function getSchema(): \Edde\Schema\ISchema {
			if ($this->schema) {
				return $this->schema;
			}
			$propertyList = [];
			foreach ($this->propertyBuilders as $name => $propertyBuilder) {
				$propertyList[$name] = $propertyBuilder->getProperty();
			}
			return $this->schema = new Schema(
				$this->node->getAttribute('name'),
				$propertyList,
				(bool)$this->node->getAttribute('is-relation', false),
				$this->node->getAttribute('alias')
			);
		}

		/** @inheritdoc */
		public function getLinkBuilders(): array {
			return $this->linkBuilders;
		}
	}
