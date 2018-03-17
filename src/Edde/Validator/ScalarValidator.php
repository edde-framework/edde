<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	class ScalarValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			if (is_scalar($value) === false) {
				if (is_object($value)) {
					throw new ValidationException(
						sprintf("Value must be scalar value, but it's an object of type [%s].", get_class($value)),
						$options['::name'] ?? null
					);
				} else if (is_resource($value)) {
					throw new ValidationException(
						"Value must be scalar value, but it's a resource.",
						$options['::name'] ?? null
					);
				}
				throw new ValidationException(
					sprintf('Given value of type [%s] is not scalar value.', gettype($value)),
					$options['::name'] ?? null
				);
			}
		}
	}
