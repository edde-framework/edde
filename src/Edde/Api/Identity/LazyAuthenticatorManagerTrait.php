<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	/**
	 * Lazy indentity dependency.
	 */
	trait LazyAuthenticatorManagerTrait {
		/**
		 * @var IAuthenticatorManager
		 */
		protected $authenticatorManager;

		/**
		 * @param IAuthenticatorManager $authenticatorManager
		 */
		public function lazyAuthenticatorManager(IAuthenticatorManager $authenticatorManager) {
			$this->authenticatorManager = $authenticatorManager;
		}
	}
