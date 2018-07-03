<?php
	declare(strict_types=1);

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

		public function session(): ISession {
			if ($this->session === null) {
				$this->sessionManager->setup();
				$this->session = $this->sessionManager->getSession(static::class);
			}
			return $this->session;
		}
	}
