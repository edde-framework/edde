<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Exception\Validator\ValidationException;
	use Edde\Object;

	abstract class AbstractValidator extends Object implements IValidator {
		/** @inheritdoc */
		public function isValid($value, array $options = []): bool {
			try {
				$this->validate($value, $options);
				return true;
			} catch (ValidationException $exception) {
				return false;
			}
		}
	}