<?php
	declare(strict_types=1);
	namespace Edde\Api\Crypt\Inject;

	use Edde\Api\Crypt\IPasswordService;

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
