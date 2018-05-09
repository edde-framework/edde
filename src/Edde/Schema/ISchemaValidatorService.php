<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Config\IConfigurable;
	use stdClass;

	interface ISchemaValidatorService extends IConfigurable {
		/**
		 * @param ISchema     $schema
		 * @param stdClass    $stdClass
		 * @param string|null $context
		 *
		 * @throws SchemaValidationException
		 */
		public function validate(ISchema $schema, stdClass $stdClass, string $context = null): void;
	}
