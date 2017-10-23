<?php
	namespace Edde\Common\Upgrade;

		use Edde\Api\Storage\Inject\Storage;
		use Edde\Api\Upgrade\Exception\CurrentVersionException;
		use Edde\Api\Upgrade\Exception\InvalidVersionException;
		use Edde\Api\Upgrade\Exception\NoUpgradesAvailableException;
		use Edde\Api\Upgrade\Exception\UnknownVersionException;
		use Edde\Api\Upgrade\IUpgrade;
		use Edde\Api\Upgrade\IUpgradeManager;
		use Edde\Common\Object\Object;

		abstract class AbstractUpgradeManager extends Object implements IUpgradeManager {
			use Storage;
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
			public function upgrade(string $version = null): IUpgrade {
				$last = null;
				try {
					$this->onUpgradeStart();
					if (empty($this->upgradeList)) {
						throw new NoUpgradesAvailableException('Cannot run upgrade: there are no available upgrades.' . ($this->isSetup() ? ' Upgrade manager is not probably properly configured.' : ' Setup has not been run, try call setup on [' . static ::class . ']'));
					}
					if ($version === null) {
						end($this->upgradeList);
						$version = key($this->upgradeList);
					}
					if (isset($this->upgradeList[$version]) === false) {
						throw new UnknownVersionException(sprintf('Requested unknown version [%s] for upgrade; there is no such upgrade available.', $version));
					}
					$upgradeList = array_keys($this->upgradeList);
					if (($current = $this->getVersion()) !== null && array_search($current, $upgradeList, true) > array_search($version, $upgradeList, true)) {
						throw new InvalidVersionException(sprintf('Current version [%s] is newer than requested version [%s] for upgrade.', $current, $version));
					}
					$upgradeList = $current ? array_slice($this->upgradeList, $index = array_search($current, array_keys($this->upgradeList), true) + 1, null, true) : $this->upgradeList;
					if (isset($index) && $index >= count($this->upgradeList)) {
						throw new CurrentVersionException(sprintf('Version [%s] is current; no upgrades has been run.', $current));
					}
					if (empty($upgradeList)) {
						throw new NoUpgradesAvailableException(sprintf('There are no upgrades available for the version [%s].', $version));
					}
					$upgrade = null;
					foreach ($upgradeList as $upgrade) {
						($last = $upgrade)->upgrade();
						$this->onUpgrade($upgrade);
						if ($upgrade->getVersion() === $version) {
							break;
						}
					}
					$this->onUpgradeEnd($upgrade);
					return $upgrade;
				} catch (\Throwable $throwable) {
					$this->onUpgradeFailed($throwable, $last);
					throw $throwable;
				}
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(string $version = null): IUpgrade {
			}

			protected function onUpgradeStart(): void {
				$this->storage->start();
			}

			protected function onUpgrade(IUpgrade $upgrade): void {
			}

			protected function onUpgradeEnd(IUpgrade $upgrade): void {
				$this->storage->commit();
			}

			protected function onUpgradeFailed(\Throwable $throwable, IUpgrade $upgrade = null): void {
				$this->storage->rollback();
			}
		}
