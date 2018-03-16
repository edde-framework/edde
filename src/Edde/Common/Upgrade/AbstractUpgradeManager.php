<?php
	declare(strict_types=1);
	namespace Edde\Common\Upgrade;

	use Edde\Api\Upgrade\Exception\CurrentVersionException;
	use Edde\Api\Upgrade\Exception\InvalidVersionException;
	use Edde\Api\Upgrade\Exception\NoUpgradesAvailableException;
	use Edde\Api\Upgrade\Exception\UnknownVersionException;
	use Edde\Api\Upgrade\Exception\UpgradeException;
	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Object\Object;
	use Edde\Inject\Log\LogService;
	use Edde\Inject\Storage\Storage;

	abstract class AbstractUpgradeManager extends Object implements IUpgradeManager {
		use Storage;
		use LogService;
		/** @var IUpgrade[] */
		protected $upgrades = [];

		/** @inheritdoc */
		public function registerUpgrade(IUpgrade $upgrade): IUpgradeManager {
			$this->upgrades[$upgrade->getVersion()] = $upgrade;
			return $this;
		}

		/** @inheritdoc */
		public function registerUpgrades(array $upgrades): IUpgradeManager {
			foreach ($upgrades as $upgrade) {
				$this->registerUpgrade($upgrade);
			}
			return $this;
		}

		/** @inheritdoc */
		public function upgrade(string $version = null): IUpgrade {
			$last = null;
			try {
				$this->onUpgradeStart();
				if (empty($this->upgrades)) {
					throw new NoUpgradesAvailableException('Cannot run upgrade: there are no available upgrades.' . ($this->isSetup() ? ' Upgrade manager is not probably properly configured.' : ' Setup has not been run, try call setup on [' . static ::class . ']'));
				}
				if ($version === null) {
					end($this->upgrades);
					$version = key($this->upgrades);
				}
				if (isset($this->upgrades[$version]) === false) {
					throw new UnknownVersionException(sprintf('Requested unknown version [%s] for upgrade; there is no such upgrade available.', $version));
				}
				$upgrades = array_keys($this->upgrades);
				if (($current = $this->getVersion()) !== null && array_search($current, $upgrades, true) > array_search($version, $upgrades, true)) {
					throw new InvalidVersionException(sprintf('Current version [%s] is newer than requested version [%s] for upgrade.', $current, $version));
				}
				$upgrades = $current ? array_slice($this->upgrades, $index = array_search($current, array_keys($this->upgrades), true) + 1, null, true) : $this->upgrades;
				if (isset($index) && $index >= count($this->upgrades)) {
					throw new CurrentVersionException(sprintf('Version [%s] is current; no upgrades has been run.', $current));
				}
				if (empty($upgrades)) {
					throw new NoUpgradesAvailableException(sprintf('There are no upgrades available for the version [%s].', $version));
				}
				$upgrade = null;
				foreach ($upgrades as $upgrade) {
					try {
						$last = $upgrade;
						$upgrade->onStart();
						$upgrade->upgrade();
						$upgrade->onSuccess();
						$this->onUpgrade($upgrade);
						if ($upgrade->getVersion() === $version) {
							break;
						}
					} catch (\Throwable $throwable) {
						$upgrade->onFail($throwable);
					}
				}
				$this->onUpgradeEnd($upgrade);
				return $upgrade;
			} catch (\Throwable $throwable) {
				$this->onUpgradeFailed($throwable, $last);
				throw $throwable;
			}
		}

		/** @inheritdoc */
		public function rollback(string $version = null): IUpgrade {
			throw new UpgradeException('Rollback is not supported in [%s].', static::class);
		}

		protected function onUpgradeStart(): void {
		}

		protected function onUpgrade(IUpgrade $upgrade): void {
		}

		protected function onUpgradeEnd(IUpgrade $upgrade): void {
		}

		protected function onUpgradeFailed(\Throwable $throwable, IUpgrade $upgrade = null): void {
			$this->logService->exception($throwable);
		}
	}
