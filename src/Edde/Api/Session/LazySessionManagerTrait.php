<?php
	declare(strict_types = 1);

	namespace Edde\Api\Session;

	/**
	 * LAzy session manager dependency.
	 */
	trait LazySessionManagerTrait {
		/**
		 * @var ISessionManager
		 */
		protected $sessionManager;

		/**
		 * @param ISessionManager $sessionManager
		 */
		public function lazySessionManager(ISessionManager $sessionManager) {
			$this->sessionManager = $sessionManager;
		}
	}
