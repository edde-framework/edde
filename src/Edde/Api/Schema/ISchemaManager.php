<?php
	namespace Edde\Api\Schema;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Schema\Exception\UnknownSchemaException;

		interface ISchemaManager extends IConfigurable {
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
			public function getSchema(string $name): ISchema;

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
			 * filter values using a schema and filter
			 *
			 * @param string $schema
			 * @param array  $source
			 *
			 * @return array
			 */
			public function filter(string $schema, array $source): array;

			/**
			 * sanitize values using a schema and sanitizer
			 *
			 * @param string $schema
			 * @param array  $source
			 *
			 * @return array
			 */
			public function sanitize(string $schema, array $source): array;
		}
