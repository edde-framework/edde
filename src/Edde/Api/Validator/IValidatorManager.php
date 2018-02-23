<?php
	declare(strict_types=1);
	namespace Edde\Api\Validator;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Validator\Exception\UnknownValidatorException;
	use Edde\Api\Validator\Exception\ValidationException;

	interface IValidatorManager extends IConfigurable {
		/**
		 * register the given validator for the given name
		 *
		 * @param string     $name
		 * @param IValidator $validator
		 *
		 * @return IValidatorManager
		 */
		public function registerValidator(string $name, IValidator $validator): IValidatorManager;

		/**
		 * @param IValidator[] $validators
		 *
		 * @return IValidatorManager
		 */
		public function registerValidators(array $validators): IValidatorManager;

		/**
		 * is the given validator available?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasValidator(string $name): bool;

		/**
		 * @param string $name
		 *
		 * @return IValidator
		 * @throws UnknownValidatorException
		 */
		public function getValidator(string $name): IValidator;

		/**
		 * validate the given array using keys as a validator name; unknown validator names
		 * are skipped
		 *
		 * @param array $source
		 *
		 * @return IValidatorManager
		 * @throws ValidationException
		 */
		public function validate(array $source): IValidatorManager;

		/**
		 * check single value against single validator
		 *
		 * @param string $validator
		 * @param mixed  $value
		 * @param array  $options
		 *
		 * @return IValidatorManager
		 * @throws UnknownValidatorException
		 * @throws ValidationException
		 */
		public function check(string $validator, $value, array $options = []): IValidatorManager;
	}
