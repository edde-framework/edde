<?php
	declare(strict_types = 1);

	namespace Edde\Api\Upgrade;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Event\IEventBus;

	/**
	 * This class is responsible for proper application upgrades.
	 */
	interface IUpgradeManager extends IDeffered, IEventBus {
		/**
		 * register the given upgrade under it's version; exception should be thrown if version is already present
		 *
		 * @param IUpgrade $upgrade
		 * @param bool $force if true, upgrade is registered regardless of version
		 *
		 * @return IUpgradeManager
		 */
		public function registerUpgrade(IUpgrade $upgrade, bool $force = false): IUpgradeManager;

		/**
		 * set current application version; if ommitted, upgrade will run from the first upgrade
		 *
		 * @param string $currentVersion
		 *
		 * @return IUpgradeManager
		 */
		public function setCurrentVersion(string $currentVersion = null): IUpgradeManager;

		/**
		 * return current list of upgrades
		 *
		 * @return IUpgrade[]
		 */
		public function getUpgradeList(): array;

		/**
		 * run upgrade; an implementation is responsible for proper upgrade execution (for example based on a version, ...)
		 *
		 * @return IUpgrade last run upgrade
		 *
		 * @throws UpgradeException
		 */
		public function upgrade(): IUpgrade;

		/**
		 * run upgrades to the given version; if the version is not found in the current upgrade list, exception should be thrown
		 *
		 * @param string|null $version
		 *
		 * @return IUpgrade last run upgrade
		 *
		 * @throws UpgradeException
		 */
		public function upgradeTo(string $version = null): IUpgrade;
	}
