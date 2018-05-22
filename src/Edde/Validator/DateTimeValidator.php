<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use DateTime;
	use stdClass;

	class DateTimeValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, ?stdClass $options = null): void {
			if ($this->checkRequired($value, $options) && $value instanceof DateTime === false) {
				throw new ValidatorException(sprintf('Value [%s] is not instanceof DateTime.', $this->getValueName($options)));
			}
		}
	}
