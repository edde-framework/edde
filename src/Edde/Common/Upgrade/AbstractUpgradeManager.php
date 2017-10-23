<?php
	namespace Edde\Common\Upgrade;

		use Edde\Api\Storage\Inject\Storage;
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
				if (empty($this->upgradeList)) {
					throw new NoUpgradesAvailableException('Cannot run upgrade: there are no available upgrades.' . ($this->isSetup() ? ' Upgrade manager is not probably properly configured.' : ' Setup has not been run, try call setup on [' . static ::class . ']'));
				} else if ($version && isset($this->upgradeList[$version]) === null) {
					throw new UnknownVersionException(sprintf('Requested unknown version [%s] for upgrade; there is no such upgrade available.', $version));
				}
				/** @var $upgradeList IUpgrade[] */
				$upgradeList = null;
				$currentVersion = $this->getVersion();
				foreach ($this->upgradeList as $upgrade) {
					if ($upgradeList && $upgrade->getVersion() === $currentVersion) {
						throw new InvalidVersionException(sprintf('Requested version [%s] is older than current version [%s]!', $version, $currentVersion));
					} else if ($upgradeList) {
						$upgradeList[] = $upgrade;
					} else if ($upgrade->getVersion() === $version) {
						$upgradeList = [];
					} else if ($version === null) {
						$upgradeList[] = $upgrade;
					}
				}
				if (empty($upgradeList)) {
					throw new NoUpgradesAvailableException(sprintf('There are no upgrades available for the version [%s].', $version));
				}
				$this->onStartup();
				try {
					$upgrade = null;
					foreach ($upgradeList as $upgrade) {
						$upgrade->upgrade();
						$this->onUpgrade($upgrade);
					}
					$this->onCommit($upgrade);
					return $upgrade;
				} catch (\Throwable $throwable) {
					$this->onRollback($upgrade, $throwable);
					throw $throwable;
				}
			}

			/**
			 * @inheritdoc
			 */
			public function rollback(string $version = null): IUpgrade {
			}

			protected function onStartup(): void {
				$this->storage->start();
			}

			protected function onUpgrade(IUpgrade $upgrade): void {
			}

			protected function onCommit(IUpgrade $upgrade): void {
				$this->storage->commit();
			}

			/**
			 * @param IUpgrade   $upgrade
			 * @param \Throwable $throwable
			 */
			protected function onRollback(IUpgrade $upgrade, \Throwable $throwable): void {
				$this->storage->rollback();
			}
		}
