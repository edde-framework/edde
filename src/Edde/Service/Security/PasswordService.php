<?php
	declare(strict_types=1);
	namespace Edde\Service\Security;

	use Edde\Security\IPasswordService;

	trait PasswordService {
		/** @var IPasswordService */
		protected $passwordService;

		/**
		 * @param IPasswordService $passwordService
		 */
		public function injectPasswordService(IPasswordService $passwordService): void {
			$this->passwordService = $passwordService;
		}
	}
