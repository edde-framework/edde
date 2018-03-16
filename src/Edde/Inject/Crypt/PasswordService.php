<?php
	declare(strict_types=1);
	namespace Edde\Inject\Crypt;

	use Edde\Crypt\IPasswordService;

	trait PasswordService {
		/** @var \Edde\Crypt\IPasswordService */
		protected $passwordService;

		/**
		 * @param IPasswordService $passwordService
		 */
		public function lazyPasswordService(\Edde\Crypt\IPasswordService $passwordService): void {
			$this->passwordService = $passwordService;
		}
	}
