<?php
	declare(strict_types=1);
	namespace Edde\Api\Schema;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Filter\Exception\FilterException;
	use Edde\Api\Filter\Exception\UnknownFilterException;
	use Edde\Api\Generator\Exception\UnknownGeneratorException;
	use Edde\Api\Sanitizer\Exception\SanitizerException;
	use Edde\Api\Sanitizer\Exception\UnknownSanitizerException;
	use Edde\Api\Schema\Exception\PropertyException;
	use Edde\Api\Schema\Exception\UnknownPropertyException;
	use Edde\Api\Schema\Exception\UnknownSchemaException;
	use Edde\Api\Validator\Exception\UnknownValidatorException;
	use Edde\Api\Validator\Exception\ValidationException;

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
		 * @throws UnknownSchemaException
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
		 * @throws UnknownFilterException
		 * @throws FilterException
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
		 * @throws UnknownSchemaException
		 * @throws UnknownValidatorException
		 * @throws UnknownPropertyException
		 */
		public function check(string $schema, array $source): void;
	}
