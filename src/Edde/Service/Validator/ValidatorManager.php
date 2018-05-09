<?php
	declare(strict_types=1);
	namespace Edde\Service\Validator;

	use Edde\Validator\IValidatorManager;

	trait ValidatorManager {
		/** @var IValidatorManager */
		protected $validatorManager;

		/**
		 * @param IValidatorManager $validatorManager
		 */
		public function injectValidatorManager(IValidatorManager $validatorManager): void {
			$this->validatorManager = $validatorManager;
		}
	}
