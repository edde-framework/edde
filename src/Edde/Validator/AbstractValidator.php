<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Edde;

	abstract class AbstractValidator extends Edde implements IValidator {
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
