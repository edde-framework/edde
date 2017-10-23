<?php
	namespace Edde\Api\Upgrade;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Upgrade\Exception\CurrentVersionException;
		use Edde\Api\Upgrade\Exception\NoUpgradesAvailableException;
		use Edde\Api\Upgrade\Exception\UnknownVersionException;

		interface IUpgradeManager extends IConfigurable {
			/**
			 * register an upgrade; order of upgrades is important
			 *
			 * @param IUpgrade $upgrade
			 *
			 * @return IUpgradeManager
			 */
			public function registerUpgrade(IUpgrade $upgrade): IUpgradeManager;

			/**
			 * register list of upgrades
			 *
			 * @param IUpgrade[] $upgradeList
			 *
			 * @return IUpgradeManager
			 */
			public function registerUpgradeList(array $upgradeList): IUpgradeManager;

			/**
			 * run upgrade to the given version or do upgrade to latest available version
			 *
			 * @param string|null $version
			 *
			 * @return IUpgrade
			 *
			 * @throws CurrentVersionException if there is nothing to do
			 * @throws NoUpgradesAvailableException if there are no upgrades at all
			 * @throws UnknownVersionException if requested version does not exists
			 */
			public function upgrade(string $version = null): IUpgrade;

			/**
			 * @param string|null $version
			 *
			 * @return IUpgrade
			 *
			 * @throws CurrentVersionException if there is nothing to do
			 * @throws NoUpgradesAvailableException if there are no upgrades at all
			 * @throws UnknownVersionException if requested version does not exists
			 */
			public function rollback(string $version = null): IUpgrade;
		}
