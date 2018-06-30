<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use function is_bool;

	class BoolValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, array $options = null): void {
			if ($this->checkRequired($value, $options) && is_bool($value) === false) {
				throw new ValidatorException(sprintf('Value [%s] is not boolean.', $this->getValueName($options)));
			}
		}
	}
