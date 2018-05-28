<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use stdClass;
	use function is_float;

	class FloatValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, ?stdClass $options = null): void {
			if ($this->checkRequired($value, $options) && is_float($value) === false) {
				throw new ValidatorException(sprintf('Value [%s] is not float.', $this->getValueName($options)));
			}
		}
	}
