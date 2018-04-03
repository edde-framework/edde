<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Filter\FilterException;
	use Edde\Generator\GeneratorException;
	use Edde\Sanitizer\SanitizerException;
	use Edde\Validator\ValidatorException;
	use stdClass;

	interface ISchemaManager {
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
		 * @param ISchema  $schema
		 * @param stdClass $source
		 *
		 * @return stdClass
		 *
		 * @throws GeneratorException
		 */
		public function generate(ISchema $schema, stdClass $source): stdClass;

		/**
		 * filter incoming (to PHP side) values using a schema and filter
		 *
		 * @param ISchema  $schema
		 * @param stdClass $source
		 *
		 * @return stdClass
		 *
		 * @throws SchemaException
		 * @throws FilterException
		 */
		public function filter(ISchema $schema, stdClass $source): stdClass;

		/**
		 * sanitize (outgoing) values (from PHP side) using a schema and sanitizer
		 *
		 * @param ISchema  $schema
		 * @param stdClass $source
		 *
		 * @return stdClass
		 *
		 * @throws SchemaException
		 * @throws SanitizerException
		 */
		public function sanitize(ISchema $schema, stdClass $source): stdClass;

		/**
		 * is the given source data valid against the given schema?
		 *
		 * @param ISchema  $schema
		 * @param stdClass $source
		 *
		 * @return bool
		 */
		public function isValid(ISchema $schema, stdClass $source): bool;

		/**
		 * validate input and throw an exception if it's not valid
		 *
		 * @param ISchema  $schema
		 * @param stdClass $source
		 *
		 * @throws SchemaValidationException
		 * @throws ValidatorException
		 */
		public function validate(ISchema $schema, stdClass $source): void;

		/**
		 * shorthand validation (validate() method)
		 *
		 * @param string   $schema
		 * @param stdClass $source
		 *
		 * @throws SchemaException
		 * @throws SchemaValidationException
		 * @throws ValidatorException
		 */
		public function check(string $schema, stdClass $source): void;
	}
