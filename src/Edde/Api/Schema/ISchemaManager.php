<?php
	namespace Edde\Api\Schema;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Schema\Exception\UnknownSchemaException;

		interface ISchemaManager extends IConfigurable {
			/**
			 * register given schema loader (could be more of them)
			 *
			 * @param ISchemaLoader $schemaLoader
			 *
			 * @return ISchemaManager
			 */
			public function registerSchemaLoader(ISchemaLoader $schemaLoader): ISchemaManager;

			/**
			 * register the given schema
			 *
			 * @param ISchema $schema
			 *
			 * @return ISchemaManager
			 */
			public function registerSchema(ISchema $schema): ISchemaManager;

			/**
			 * register list of schemas at once
			 *
			 * @param ISchema[] $schemaList
			 *
			 * @return ISchemaManager
			 */
			public function registerSchemaList(array $schemaList): ISchemaManager;

			/**
			 * get the given schema
			 *
			 * @param string $name
			 *
			 * @return ISchema
			 *
			 * @throws UnknownSchemaException
			 */
			public function load(string $name): ISchema;

			/**
			 * generate all empty values with a generator (using a schema)
			 *
			 * @param string $schema
			 * @param array  $source
			 *
			 * @return array
			 */
			public function generate(string $schema, array $source): array;

			/**
			 * filter incoming (to PHP side) values using a schema and filter
			 *
			 * @param string $schema
			 * @param array  $source
			 *
			 * @return array
			 */
			public function filter(string $schema, array $source): array;

			/**
			 * sanitize (outgoing) values (from PHP side) using a schema and sanitizer
			 *
			 * @param string $schema
			 * @param array  $source
			 *
			 * @return array
			 */
			public function sanitize(string $schema, array $source): array;
		}
