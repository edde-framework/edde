<?php
	declare(strict_types=1);

	namespace Edde\Api\Identity;

	/**
	 * Implementation of an ACL mechanism; this should set roles to the given identity.
	 */
	interface IAuthorizator {
		/**
		 * update list of roles (ACL) of the given identity
		 *
		 * @param IIdentity $identity
		 *
		 * @return IAuthorizator
		 */
		public function authorize(IIdentity $identity): IAuthorizator;
	}
