<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Edde;
	use stdClass;

	class SchemaBuilder extends Edde implements ISchemaBuilder {
		/** @var stdClass */
		protected $source;
		/** @var IAttributeBuilder[] */
		protected $propertyBuilders = [];
		/** @var ISchema */
		protected $schema;
		/** @var ILinkBuilder[] */
		protected $linkBuilders = [];

		public function __construct(string $name) {
			$this->source = (object)['name' => $name];
		}

		/** @inheritdoc */
		public function alias(string $alias): ISchemaBuilder {
			$this->source->alias = $alias;
			return $this;
		}

		/** @inheritdoc */
		public function relation(bool $relation): ISchemaBuilder {
			$this->source->isRelation = $relation;
			return $this;
		}

		/** @inheritdoc */
		public function property(string $name): IAttributeBuilder {
			return $this->propertyBuilders[$name] = new AttributeBuilder($name);
		}

		/** @inheritdoc */
		public function primary(string $name): IAttributeBuilder {
			return $this->property($name)->primary();
		}

		/** @inheritdoc */
		public function string(string $name): IAttributeBuilder {
			return $this->property($name)->type('string');
		}

		/** @inheritdoc */
		public function text(string $name): IAttributeBuilder {
			return $this->property($name)->type('text');
		}

		/** @inheritdoc */
		public function integer(string $name): IAttributeBuilder {
			return $this->property($name)->type('int');
		}

		/** @inheritdoc */
		public function link(ILinkBuilder $linkBuilder): ISchemaBuilder {
			$this->linkBuilders[] = $linkBuilder;
			if (($this->source->isRelation ?? false) && count($this->linkBuilders) > 2) {
				throw new SchemaException(sprintf('Relation schema [%s] must have exactly two links; if you need more links, remove "relation" flag from the schema.', $this->source->name));
			}
			return $this;
		}

		/** @inheritdoc */
		public function getSchema(): ISchema {
			if ($this->schema) {
				return $this->schema;
			}
			$properties = [];
			foreach ($this->propertyBuilders as $name => $propertyBuilder) {
				$properties[$name] = $propertyBuilder->getAttribute();
			}
			return $this->schema = new Schema(
				$this->source->name,
				$properties,
				$this->source->isRelation ?? false,
				$this->source->alias ?? null
			);
		}

		/** @inheritdoc */
		public function getLinkBuilders(): array {
			return $this->linkBuilders;
		}
	}
