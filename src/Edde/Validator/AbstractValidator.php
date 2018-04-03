<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Obj3ct;

	abstract class AbstractValidator extends Obj3ct implements IValidator {
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
