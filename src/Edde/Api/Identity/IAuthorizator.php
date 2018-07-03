<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	use Edde\Api\Deffered\IDeffered;

	/**
	 * Implementation of an ACL mechanism; this should set roles to the given identity.
	 */
	interface IAuthorizator extends IDeffered {
		/**
		 * update list of roles (ACL) of the given identity
		 *
		 * @param IIdentity $identity
		 *
		 * @return IAuthorizator
		 */
		public function authorize(IIdentity $identity): IAuthorizator;
	}
