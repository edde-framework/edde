<?php
	declare(strict_types=1);

	namespace Edde\Api\Session\Inject;

	use Edde\Api\Session\ISessionManager;

	/**
	 * LAzy session manager dependency.
	 */
	trait SessionManager {
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
