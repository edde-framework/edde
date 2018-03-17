<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Config\IConfigurable;
	use Edde\Validator\ValidatorException;

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
		 * @param ISchema[] $schemas
		 *
		 * @return ISchemaManager
		 */
		public function registerSchemas(array $schemas): ISchemaManager;

		/**
		 * try to load the given schema and return true/false if it exists
		 *
		 * @param string $schema
		 *
		 * @return bool
		 */
		public function hasSchema(string $schema): bool;

		/**
		 * get the given schema
		 *
		 * @param string $name
		 *
		 * @return ISchema
		 *
		 * @throws SchemaException
		 */
		public function load(string $name): ISchema;

		/**
		 * generate all empty values with a generator (using a schema)
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @return array
		 */
		public function generate(ISchema $schema, array $source): array;

		/**
		 * filter incoming (to PHP side) values using a schema and filter
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @return array
		 *
		 * @throws SchemaException
		 */
		public function filter(ISchema $schema, array $source): array;

		/**
		 * sanitize (outgoing) values (from PHP side) using a schema and sanitizer
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @return array
		 *
		 * @throws SchemaException
		 */
		public function sanitize(ISchema $schema, array $source): array;

		/**
		 * is the given source data valid against the given schema?
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @return bool
		 */
		public function isValid(ISchema $schema, array $source): bool;

		/**
		 * validate input and throw an exception if it's not valid
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @throws SchemaValidationException
		 * @throws ValidatorException
		 */
		public function validate(ISchema $schema, array $source): void;

		/**
		 * shorthand validation (validate() method)
		 *
		 * @param string $schema
		 * @param array  $source
		 *
		 * @throws SchemaException
		 * @throws SchemaValidationException
		 * @throws ValidatorException
		 */
		public function check(string $schema, array $source): void;
	}
