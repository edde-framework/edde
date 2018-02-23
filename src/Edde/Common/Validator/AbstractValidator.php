<?php
	declare(strict_types=1);
	namespace Edde\Common\Validator;

	use Edde\Api\Validator\Exception\ValidationException;
	use Edde\Api\Validator\IValidator;
	use Edde\Common\Object\Object;

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
