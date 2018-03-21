<?php
	declare(strict_types=1);
	namespace Edde\Upgrade;

	use Edde\Collection\ICollection;
	use Edde\Config\IConfigurable;
	use Edde\Entity\IEntity;

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
		 * @throws UpgradeException
		 * @throws CurrentVersionException
		 */
		public function upgrade(string $version = null): IUpgrade;

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
		public function getCurrentCollection(): ICollection;
	}
