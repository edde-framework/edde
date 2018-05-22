<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use stdClass;
	use function is_int;

	class IntValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, ?stdClass $options = null): void {
			if (is_int($value) === false) {
				throw new ValidatorException(sprintf('Value [%s] is not integer.', $this->getValueName($options)));
			}
		}
	}