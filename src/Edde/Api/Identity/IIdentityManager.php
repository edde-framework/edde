<?php
	declare(strict_types=1);

	namespace Edde\Api\Identity;

	use Edde\Api\Storage\IRepository;

	interface IIdentityManager extends IIdentity, IRepository {
		/**
		 * push identity to the session or replace current identity by the given one
		 *
		 * @param IIdentity|null $identity
		 *
		 * @return IIdentityManager
		 */
		public function update(IIdentity $identity = null): IIdentityManager;
	}
