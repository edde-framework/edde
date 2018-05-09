<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Edde;

	class ValidatorManager extends Edde implements IValidatorManager {
		/** @var IValidator[] */
		protected $validators = [];

		/** @inheritdoc */
		public function registerValidator(string $name, IValidator $validator): IValidatorManager {
			$this->validators[$name] = $validator;
			return $this;
		}

		/** @inheritdoc */
		public function registerValidators(array $validators): IValidatorManager {
			foreach ($validators as $name => $validator) {
				$this->registerValidator($name, $validator);
			}
			return $this;
		}

		/** @inheritdoc */
		public function validate(string $name, $value): void {
			if (isset($this->validators[$name]) === false) {
				throw new ValidatorException(sprintf('Requested unknown validator [%s].', $name));
			}
			$this->validators[$name]->validate($value);
		}
	}
