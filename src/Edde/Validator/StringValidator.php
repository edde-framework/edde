<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use stdClass;
	use function is_string;

	class StringValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, ?stdClass $options = null): void {
			if (is_string($value) === false) {
				throw new ValidatorException(sprintf('Value [%s] is not string.', $this->getValueName($options)));
			}
		}
	}
