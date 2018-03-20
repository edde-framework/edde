<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use function is_int;

	class FloatValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			if (is_float($value) === false || is_int($value) === false) {
				throw new ValidationException(
					vsprintf('Given value%s of type [%s] is not a float!', [
						isset($options['::name']) ? ' [' . $options['::name'] . ']' : '',
						gettype($value),
					]),
					$options['::name'] ?? null
				);
			}
		}
	}
