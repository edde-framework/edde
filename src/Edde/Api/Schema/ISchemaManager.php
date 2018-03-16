<?php
	declare(strict_types=1);
	namespace Edde\Api\Schema;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Validator\Exception\UnknownValidatorException;
	use Edde\Api\Validator\Exception\ValidationException;
	use Edde\Exception\Generator\UnknownGeneratorException;
	use Edde\Exception\Sanitizer\SanitizerException;
	use Edde\Exception\Sanitizer\UnknownSanitizerException;
	use Edde\Exception\Schema\PropertyException;
	use Edde\Exception\Schema\UnknownPropertyException;

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
		 * @throws PropertyException
		 */
		public function hasSchema(string $schema): bool;

		/**
		 * get the given schema
		 *
		 * @param string $name
		 *
		 * @return ISchema
		 *
		 * @throws \Edde\Exception\Schema\UnknownSchemaException
		 * @throws UnknownPropertyException
		 */
		public function load(string $name): ISchema;

		/**
		 * generate all empty values with a generator (using a schema)
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @return array
		 * @throws UnknownGeneratorException
		 */
		public function generate(ISchema $schema, array $source): array;

		/**
		 * filter incoming (to PHP side) values using a schema and filter
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @return array
		 * @throws UnknownPropertyException
		 * @throws \Edde\Exception\Filter\UnknownFilterException
		 * @throws \Edde\Exception\Filter\FilterException
		 */
		public function filter(ISchema $schema, array $source): array;

		/**
		 * sanitize (outgoing) values (from PHP side) using a schema and sanitizer
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @return array
		 * @throws UnknownPropertyException
		 * @throws UnknownSanitizerException
		 * @throws SanitizerException
		 */
		public function sanitize(ISchema $schema, array $source): array;

		/**
		 * is the given source data valid against the given schema?
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @return bool
		 * @throws UnknownValidatorException
		 */
		public function isValid(ISchema $schema, array $source): bool;

		/**
		 * validate input and throw an exception if it's not valid
		 *
		 * @param ISchema $schema
		 * @param array   $source
		 *
		 * @throws ValidationException
		 * @throws UnknownValidatorException
		 */
		public function validate(ISchema $schema, array $source): void;

		/**
		 * shorthand validation (validate() method)
		 *
		 * @param string $schema
		 * @param array  $source
		 *
		 * @throws ValidationException
		 * @throws \Edde\Exception\Schema\UnknownSchemaException
		 * @throws UnknownValidatorException
		 * @throws UnknownPropertyException
		 */
		public function check(string $schema, array $source): void;
	}
