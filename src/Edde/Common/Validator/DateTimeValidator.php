<?php
	declare(strict_types=1);
	namespace Edde\Common\Validator;

	use Edde\Api\Validator\Exception\ValidationException;

	class DateTimeValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			if ($value instanceof \DateTime === false) {
				throw new ValidationException(
					vsprintf('Given value%s of type [%s] is not an instance of DateTime!', [
						isset($options['::name']) ? ' [' . $options['::name'] . ']' : '',
						is_object($value) ? get_class($value) : gettype($value),
					]),
					$options['::name'] ?? null
				);
			}
		}
	}
