<?php
	declare(strict_types=1);
	namespace Edde\Api\Upgrade;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Entity\ICollection;
	use Edde\Api\Entity\IEntity;
	use Edde\Exception\Upgrade\CurrentVersionException;
	use Edde\Exception\Upgrade\NoUpgradesAvailableException;
	use Edde\Exception\Upgrade\UnknownVersionException;
	use Throwable;

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
		 * @param IUpgrade[] $upgrades
		 *
		 * @return IUpgradeManager
		 */
		public function registerUpgrades(array $upgrades): IUpgradeManager;

		/**
		 * run upgrade to the given version or do upgrade to latest available version
		 *
		 * @param string|null $version
		 *
		 * @return IUpgrade
		 *
		 * @throws CurrentVersionException if there is nothing to do
		 * @throws \Edde\Exception\Upgrade\NoUpgradesAvailableException if there are no upgrades at all
		 * @throws UnknownVersionException if requested version does not exists
		 * @throws Throwable
		 */
		public function upgrade(string $version = null): IUpgrade;

		/**
		 * @param string|null $version
		 *
		 * @return IUpgrade
		 *
		 * @throws \Edde\Exception\Upgrade\CurrentVersionException if there is nothing to do
		 * @throws \Edde\Exception\Upgrade\NoUpgradesAvailableException if there are no upgrades at all
		 * @throws UnknownVersionException if requested version does not exists
		 */
		public function rollback(string $version = null): IUpgrade;

		/**
		 * get current version (basically current version of an application); if null
		 * is returned, application is in "zero state", thus nothing nowhere
		 *
		 * @return string|null
		 */
		public function getVersion(): ?string;

		/**
		 * get current list of installed upgrades
		 *
		 * @return ICollection|IEntity[]
		 */
		public function getCurrentList(): ICollection;
	}
