<?php
	declare(strict_types=1);
	namespace Edde\Common\Validator;

	use Edde\Exception\Validator\ValidationException;

	class EmailValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			if (is_string($value) === false) {
				throw new ValidationException(
					vsprintf('Given value%s of type [%s] is not a string!', [
						isset($options['::name']) ? ' [' . $options['::name'] . ']' : '',
						gettype($value),
					]),
					$options['::name'] ?? null
				);
			} else if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
				throw new ValidationException(
					vsprintf('Given value%s [%s] is not an email!', [
						isset($options['::name']) ? ' [' . $options['::name'] . ']' : '',
						$value,
					]),
					$options['::name'] ?? null
				);
			}
		}
	}
