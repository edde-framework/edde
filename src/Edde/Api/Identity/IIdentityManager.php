<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Storage\IRepository;

	interface IIdentityManager extends IRepository, IDeffered {
		/**
		 * return current identity build from session
		 *
		 * @return IIdentity
		 */
		public function identity(): IIdentity;

		/**
		 * update session data with current identity
		 *
		 * @return IIdentityManager
		 */
		public function update(): IIdentityManager;

		/**
		 * reset current identity to default state (drop current session data); hard reset will clear data of current
		 *
		 * @param bool $hard if true, data in current identity will be reset too
		 *
		 * @return IIdentityManager
		 */
		public function reset(bool $hard = true): IIdentityManager;
	}
