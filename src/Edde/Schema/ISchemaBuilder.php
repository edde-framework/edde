<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	interface ISchemaBuilder {
		/**
		 * set alias to this schema; later in queries name or alias could be used
		 *
		 * @param string $alias
		 *
		 * @return ISchemaBuilder
		 */
		public function alias(string $alias): ISchemaBuilder;

		/**
		 * mark a schema as relational schema
		 *
		 * @param bool $relation
		 *
		 * @return ISchemaBuilder
		 */
		public function relation(bool $relation): ISchemaBuilder;

		/**
		 * create a new property with the given name
		 *
		 * @param string $name
		 *
		 * @return IAttributeBuilder
		 */
		public function property(string $name): IAttributeBuilder;

		/**
		 * create a primary property on the schema
		 *
		 * @param string $name
		 *
		 * @return IAttributeBuilder
		 */
		public function primary(string $name): IAttributeBuilder;

		/**
		 * create a string type property with the given name
		 *
		 * @param string $name
		 *
		 * @return IAttributeBuilder
		 */
		public function string(string $name): IAttributeBuilder;

		/**
		 * create a text type property (should be unlimited text field)
		 *
		 * @param string $name
		 *
		 * @return IAttributeBuilder
		 */
		public function text(string $name): IAttributeBuilder;

		/**
		 * create a common integer property
		 *
		 * @param string $name
		 *
		 * @return IAttributeBuilder
		 */
		public function integer(string $name): IAttributeBuilder;

		/**
		 * add link builder
		 *
		 * @param ILinkBuilder $linkBuilder
		 *
		 * @return ISchemaBuilder
		 *
		 * @throws SchemaException
		 */
		public function link(ILinkBuilder $linkBuilder): ISchemaBuilder;

		/**
		 * build and return a schema
		 *
		 * @return ISchema
		 */
		public function getSchema(): ISchema;

		/**
		 * @return ILinkBuilder[]
		 */
		public function getLinkBuilders(): array;
	}
