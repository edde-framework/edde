<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Exception\Validator\ValidationException;

	class StringValidator extends AbstractValidator {
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
			}
		}
	}
