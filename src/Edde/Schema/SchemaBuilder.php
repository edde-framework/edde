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

		public function __construct(string $name) {
			$this->source = (object)['name' => $name];
		}

		/** @inheritdoc */
		public function alias(string $alias): ISchemaBuilder {
			$this->source->alias = $alias;
			return $this;
		}

		/** @inheritdoc */
		public function meta(array $meta): ISchemaBuilder {
			$this->source->meta = $meta;
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
		public function create(): ISchema {
			if ($this->schema) {
				return $this->schema;
			}
			$attributes = [];
			foreach ($this->propertyBuilders as $name => $propertyBuilder) {
				$attributes[$name] = $propertyBuilder->getAttribute();
			}
			return $this->schema = new Schema(
				$this->source->name,
				$attributes,
				$this->source->meta,
				$this->source->alias ?? null
			);
		}
	}
