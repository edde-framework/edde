<?php
	declare(strict_types=1);

	namespace Edde\Api\Identity;

	use Edde\Api\Config\IConfigurable;

	interface IAuthenticatorManager extends IConfigurable {
		/**
		 * @param IAuthenticator $authenticator
		 *
		 * @return IAuthenticatorManager
		 */
		public function registerAuthenticator(IAuthenticator $authenticator): IAuthenticatorManager;

		/**
		 * optionaly define authentication flow (support for chained login methods - credentials + sms, ...)
		 *
		 * @param string   $initial
		 * @param string[] ...$authenticatorList
		 *
		 * @return IAuthenticatorManager
		 */
		public function registerStep(string $initial, string ...$authenticatorList): IAuthenticatorManager;

		/**
		 * register flow list; array key is name of flow, value is array of flows; can be ampty array
		 *
		 * @param array $stepList
		 *
		 * @return IAuthenticatorManager
		 */
		public function registerStepList(array $stepList): IAuthenticatorManager;

		/**
		 * execute an authentication flow; if the flow fails, $flow will be used as initial authenticator
		 *
		 * @param string $step
		 * @param array  ...$credentials
		 *
		 * @return IAuthenticatorManager
		 */
		public function step(string $step, ...$credentials): IAuthenticatorManager;

		/**
		 * handy method to check if there are remaining steps (so isDone() === true if all auth. is done)
		 *
		 * @return bool
		 */
		public function isDone(): bool;

		/**
		 * return upcoming list of steps or empty array when there is no more steps
		 *
		 * @return string[]
		 */
		public function getStepList(): array;

		/**
		 * return name of a current authenticator or exception when there is no active step
		 *
		 * @return string
		 */
		public function getCurrentStep(): string;

		/**
		 * authenticator manager should keep current step state even when exception; this should restart the selected step
		 *
		 * @return IAuthenticatorManager
		 */
		public function reset(): IAuthenticatorManager;

		/**
		 * similar to self::reset() but isDone() will return true and self::getCurrentStep() will return initial authenticator
		 *
		 * @param string $step
		 *
		 * @return IAuthenticatorManager
		 */
		public function select(string $step): IAuthenticatorManager;

		/**
		 * try to use named authenticator for authenticate the given identity
		 *
		 * @param string $name
		 * @param array  ...$credentials
		 *
		 * @return IAuthenticatorManager
		 */
		public function authenticate(string $name, ...$credentials): IAuthenticatorManager;
	}
