<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use stdClass;
	use function is_bool;

	class BoolValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, ?stdClass $options = null): void {
			if (is_bool($value) === false) {
				throw new ValidatorException(sprintf('Value [%s] is not boolean.', $this->getValueName($options)));
			}
		}
	}
