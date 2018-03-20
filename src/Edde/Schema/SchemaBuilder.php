<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Collection\HashMap;
	use Edde\Collection\IHashMap;

	class SchemaBuilder extends HashMap implements ISchemaBuilder {
		/** @var IHashMap[] */
		protected $properties = [];
		/** @var IPropertyBuilder[] */
		protected $propertyBuilders = [];
		/** @var ISchema */
		protected $schema;
		/** @var ILinkBuilder[] */
		protected $linkBuilders = [];

		public function __construct(string $name) {
			parent::__construct((object)['name' => $name]);
		}

		/** @inheritdoc */
		public function alias(string $alias): ISchemaBuilder {
			$this->set('alias', $alias);
			return $this;
		}

		/** @inheritdoc */
		public function relation(bool $relation): ISchemaBuilder {
			$this->set('is-relation', $relation);
			return $this;
		}

		/** @inheritdoc */
		public function property(string $name): IPropertyBuilder {
			return $this->propertyBuilders[$name] = new PropertyBuilder($this, $name);
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
			if ($this->get('is-relation', false) && count($this->linkBuilders) > 2) {
				throw new SchemaException(sprintf('Relation schema [%s] must have exactly two links; if you need more links, remove "relation" flag from the schema.', $this->get('name')));
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
				$properties[$name] = $propertyBuilder->getProperty();
			}
			return $this->schema = new Schema(
				(string)$this->get('name'),
				$properties,
				(bool)$this->get('is-relation', false),
				$this->get('alias')
			);
		}

		/** @inheritdoc */
		public function getLinkBuilders(): array {
			return $this->linkBuilders;
		}
	}
