<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Edde;
	use stdClass;
	use function property_exists;

	abstract class AbstractValidator extends Edde implements IValidator {
		protected function getValueName(?stdClass $stdClass): string {
			return property_exists($stdClass, '::name') ? (string)$stdClass->{'::name'} : '<unknown value name, use ::name in validator $options to set name>';
		}
	}
