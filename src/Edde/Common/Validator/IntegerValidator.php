<?php
	declare(strict_types=1);
	namespace Edde\Common\Validator;

	use Edde\Api\Validator\Exception\ValidationException;

	class IntegerValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			if (is_int($value) === false) {
				throw new ValidationException(
					vsprintf('Given value%s of type [%s] is not an integer!', [
						isset($options['::name']) ? ' [' . $options['::name'] . ']' : '',
						gettype($value),
					]),
					$options['::name'] ?? null
				);
			}
		}
	}
