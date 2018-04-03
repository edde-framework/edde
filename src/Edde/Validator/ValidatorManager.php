<?php
	declare(strict_types=1);
	namespace Edde\Validator;

	use Edde\Service\AbstractService;

	class ValidatorManager extends AbstractService implements IValidatorManager {
		/** @var IValidator[] */
		protected $validators = [];

		/** @inheritdoc */
		public function registerValidator(string $name, IValidator $sanitizer): IValidatorManager {
			$this->validators[$name] = $sanitizer;
			return $this;
		}

		/** @inheritdoc */
		public function registerValidators(array $validators): IValidatorManager {
			foreach ($validators as $name => $sanitizer) {
				$this->registerValidator($name, $sanitizer);
			}
			return $this;
		}

		/** @inheritdoc */
		public function hasValidator(string $name): bool {
			return isset($this->validators[$name]);
		}

		/** @inheritdoc */
		public function getValidator(string $name): IValidator {
			if (isset($this->validators[$name]) === false) {
				throw new ValidatorException(sprintf('Requested unknown validator [%s].', $name));
			}
			return $this->validators[$name];
		}

		/** @inheritdoc */
		public function validate(array $source): IValidatorManager {
			foreach ($source as $k => $v) {
				if (isset($this->validators[$k])) {
					$this->validators[$k]->validate($v);
				}
			}
			return $this;
		}

		/** @inheritdoc */
		public function check(string $validator, $value, array $options = []): IValidatorManager {
			$this->getValidator($validator)->validate($value, $options);
			return $this;
		}
	}
