<?php
declare(strict_types=1);

namespace Edde\Upgrade;

use Edde\Configurable\IConfigurable;

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
}
