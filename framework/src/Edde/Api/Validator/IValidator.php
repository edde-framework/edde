<?php
	declare(strict_types=1);
	namespace Edde\Api\Validator;

	use Edde\Api\Validator\Exception\BatchValidationException;
	use Edde\Api\Validator\Exception\UnknownValidatorException;
	use Edde\Api\Validator\Exception\ValidationException;

	interface IValidator {
		/**
		 * soft way, how to validate a value
		 *
		 * @param mixed $value
		 * @param array $options
		 *
		 * @return bool
		 */
		public function isValid($value, array $options = []): bool;

		/**
		 * execute value validation; value could be very simple scalar or even very
		 * complex object; in general validator should be as simple as possible
		 *
		 * @param mixed $value
		 * @param array $options
		 *
		 * @return mixed
		 * @throws ValidationException
		 * @throws BatchValidationException
		 * @throws UnknownValidatorException
		 */
		public function validate($value, array $options = []): void;
	}
