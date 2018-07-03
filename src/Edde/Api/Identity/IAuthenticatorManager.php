<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	use Edde\Api\Deffered\IDeffered;

	interface IAuthenticatorManager extends IDeffered {
		/**
		 * @param IAuthenticator $authenticator
		 *
		 * @return IAuthenticatorManager
		 */
		public function registerAuthenticator(IAuthenticator $authenticator): IAuthenticatorManager;

		/**
		 * optionaly define authentication flow (support for chained login methods - credentials + sms, ...)
		 *
		 * @param string $initial
		 * @param string[] ...$authenticatorList
		 *
		 * @return IAuthenticatorManager
		 */
		public function registerFlow(string $initial, string ...$authenticatorList): IAuthenticatorManager;

		/**
		 * register flow list; array key is name of flow, value is array of flows; can be ampty array
		 *
		 * @param array $flowList
		 *
		 * @return IAuthenticatorManager
		 */
		public function registerFlowList(array $flowList): IAuthenticatorManager;

		/**
		 * execute an authentication flow; if the flow fails, $flow will be used as initial authenticator
		 *
		 * @param string $flow
		 * @param array ...$credentials
		 *
		 * @return IAuthenticatorManager
		 */
		public function flow(string $flow, ...$credentials): IAuthenticatorManager;

		/**
		 * handy method to check if there is opened flow
		 *
		 * @return bool
		 */
		public function hasFlow(): bool;

		/**
		 * return upcoming flow or ampty array when there is no more flow
		 *
		 * @return string[]
		 */
		public function getFlow(): array;

		/**
		 * return name of a current authenticator or null when there is no flow
		 *
		 * @return string
		 */
		public function getCurrentFlow();

		/**
		 * authenticator manager should keep current flow state even when exception; this should restart the selected flow
		 *
		 * @return IAuthenticatorManager
		 */
		public function reset(): IAuthenticatorManager;

		/**
		 * similar to self::reset() but hasFlow() will return true and self::getCurrentFlow() will return initial authenticator
		 *
		 * @param string $flow
		 *
		 * @return IAuthenticatorManager
		 */
		public function select(string $flow): IAuthenticatorManager;

		/**
		 * try to use named authenticator for authenticate the given identity
		 *
		 * @param string $name
		 * @param array ...$credentials
		 *
		 * @return IAuthenticatorManager
		 */
		public function authenticate(string $name, ...$credentials): IAuthenticatorManager;
	}
