<?php
	declare(strict_types=1);
	namespace Edde\Common\Validator;

	use Edde\Api\Validator\Exception\ValidationException;

	class BoolValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			if (is_bool($value) === false) {
				throw new ValidationException(
					vsprintf('Given value%s of type [%s] is not a boolean!', [
						isset($options['::name']) ? ' [' . $options['::name'] . ']' : '',
						gettype($value),
					]),
					$options['::name'] ?? null
				);
			}
		}
	}
