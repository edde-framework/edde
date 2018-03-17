<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	class RequiredValidator extends AbstractValidator {
		/** @inheritdoc */
		public function validate($value, array $options = []): void {
			if ($value === null) {
				throw new ValidationException(
					sprintf('Required value%s is NULL.', isset($options['::name']) ? ' [' . $options['::name'] . ']' : ''),
					$options['::name'] ?? null
				);
			}
		}
	}
