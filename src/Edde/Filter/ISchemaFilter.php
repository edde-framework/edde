<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Config\IConfigurable;
	use Edde\Schema\ISchema;
	use stdClass;

	interface ISchemaFilter extends IConfigurable {
		/**
		 * filter an object by schema's filters {filter, generator, sanitizer, ...)
		 *
		 * @param ISchema     $schema
		 * @param stdClass    $stdClass
		 * @param string|null $context
		 *
		 * @return stdClass
		 *
		 * @throws FilterException
		 */
		public function input(ISchema $schema, stdClass $stdClass, string $context = null): stdClass;

		/**
		 * @param ISchema     $schema
		 * @param stdClass    $stdClass
		 * @param string|null $context
		 *
		 * @return stdClass
		 *
		 * @throws FilterException
		 */
		public function output(ISchema $schema, stdClass $stdClass, string $context = null): stdClass;
	}
