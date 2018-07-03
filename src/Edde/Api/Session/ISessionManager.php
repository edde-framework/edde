<?php
	declare(strict_types = 1);

	namespace Edde\Api\Session;

	use Edde\Api\Deffered\IDeffered;

	/**
	 * Session manager is responsible for updating session state (starting, modifying, closing, ...).
	 */
	interface ISessionManager extends IDeffered {
		/**
		 * excplicitly open a session
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
		 * return reference to the current session root ($_SESSION superglobal)
		 *
		 * @param string $name
		 *
		 * @return array
		 */
		public function &session(string $name): array;

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
	}
