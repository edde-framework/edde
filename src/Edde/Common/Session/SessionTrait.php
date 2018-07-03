<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISession;
	use Edde\Api\Session\LazySessionManagerTrait;

	/**
	 * Helper trait for simple work with session section.
	 */
	trait SessionTrait {
		use LazySessionManagerTrait;
		/**
		 * @var ISession
		 */
		protected $session;

		protected function session() {
			$this->lazy('session', function () {
				return $this->sessionManager->getSession(static::class);
			});
		}
	}
