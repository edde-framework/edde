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
		 * set meta data for the schema
		 *
		 * @param array $meta
		 *
		 * @return ISchemaBuilder
		 */
		public function meta(array $meta): ISchemaBuilder;

		/**
		 * create a new property with the given name
		 *
		 * @param string $name
		 *
		 * @return IAttributeBuilder
		 */
		public function property(string $name): IAttributeBuilder;

		/**
		 * mark this schema as a relation (source)->(target)
		 *
		 * @param string $source
		 * @param string $target
		 *
		 * @return ISchemaBuilder
		 */
		public function relation(string $source, string $target): ISchemaBuilder;

		/**
		 * build and return a schema
		 *
		 * @return ISchema
		 */
		public function create(): ISchema;
	}
