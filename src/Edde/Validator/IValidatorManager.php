<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Configurable\IConfigurable;
	use stdClass;

	interface IValidatorManager extends IConfigurable {
		/**
		 * register a validator
		 *
		 * @param string     $name
		 * @param IValidator $validator
		 *
		 * @return IValidatorManager
		 */
		public function registerValidator(string $name, IValidator $validator): IValidatorManager;

		/**
		 * register multiple validators (name => validator); method should NOT replace already registered
		 * validators
		 *
		 * @param array $validators
		 *
		 * @return IValidatorManager
		 */
		public function registerValidators(array $validators): IValidatorManager;

		/**
		 * @param string        $name
		 * @param mixed         $value
		 * @param stdClass|null $options
		 *
		 * @throws ValidatorException
		 */
		public function validate(string $name, $value, ?stdClass $options = null): void;
	}
