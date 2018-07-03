<?php
	declare(strict_types=1);

	namespace Edde\Api\Session;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Session manager is responsible for updating session state (starting, modifying, closing, ...).
	 */
	interface ISessionManager extends IConfigurable {
		/**
		 * explicitly open a session
		 *
		 * @return ISessionManager
		 */
		public function start(): ISessionManager;

		/**
		 * tells if session is opened
		 *
		 * @return bool
		 */
		public function isSession(): bool;

		/**
		 * return a new session with the given name; this may start a session
		 *
		 * @param string $name
		 *
		 * @return ISession
		 */
		public function getSession(string $name): ISession;

		/**
		 * clear the current session
		 *
		 * @return ISessionManager
		 */
		public function clear(): ISessionManager;

		/**
		 * excplicitly close a session (to release session locks)
		 *
		 * @return ISessionManager
		 */
		public function close(): ISessionManager;

		/**
		 * return session name; commonly PHPSESSID
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * return current session ID
		 *
		 * @return string
		 */
		public function getSessionId(): string;
	}
