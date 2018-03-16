<?php
	declare(strict_types=1);
	namespace Edde\Inject\Validator;

	use Edde\Validator\IValidatorManager;

	trait ValidatorManager {
		/** @var IValidatorManager */
		protected $validatorManager;

		/**
		 * @param IValidatorManager $validatorManager
		 */
		public function lazyValidatorManager(IValidatorManager $validatorManager): void {
			$this->validatorManager = $validatorManager;
		}
	}
