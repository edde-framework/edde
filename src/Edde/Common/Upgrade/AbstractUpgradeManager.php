<?php
	namespace Edde\Common\Upgrade;

		use Edde\Api\Upgrade\Exception\NoUpgradesAvailableException;
		use Edde\Api\Upgrade\IUpgrade;
		use Edde\Api\Upgrade\IUpgradeManager;
		use Edde\Common\Object\Object;

		abstract class AbstractUpgradeManager extends Object implements IUpgradeManager {
			/**
			 * @var IUpgrade[]
			 */
			protected $upgradeList = [];

			/**
			 * @inheritdoc
			 */
			public function registerUpgrade(IUpgrade $upgrade): IUpgradeManager {
				$this->upgradeList[$upgrade->getVersion()] = $upgrade;
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function registerUpgradeList(array $upgradeList): IUpgradeManager {
				foreach ($upgradeList as $upgrade) {
					$this->registerUpgrade($upgrade);
				}
				return $this;
			}

			/**
			 * @inheritdoc
			 */
			public function upgrade(string $version = null): IUpgradeManager {
				if (empty($this->upgradeList)) {
					throw new NoUpgradesAvailableException('Cannot run upgrade: there are no available upgrades.' . ($this->isSetup() ? ' Upgrade manager is not probably properly configured.' : ' Setup has not been run, try call setup on [' . static ::class . ']'));
				}
			}
		}
