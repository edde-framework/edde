<?php
	declare(strict_types=1);

	namespace Edde\Api\Identity;

	/**
	 * This implementation is responsible for an identity authentification.
	 */
	interface IAuthenticator {
		/**
		 * name of auth method
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * authenticate a given identity or throw an exception
		 *
		 * @param IIdentity $identity if not specified, global one is used
		 * @param array     ...$credentials
		 *
		 * @return IAuthenticator
		 */
		public function authenticate(IIdentity $identity, ...$credentials): IAuthenticator;
	}
