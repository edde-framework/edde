<?php
	declare(strict_types=1);
	namespace Edde\Upgrade;

	use Edde\Edde;
	use Edde\Service\Log\LogService;
	use Throwable;

	abstract class AbstractUpgradeManager extends Edde implements IUpgradeManager {
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
					throw new UpgradeException('Cannot run upgrade: there are no available upgrades.' . ($this->isSetup() ? ' Upgrade manager is not probably properly configured.' : ' Setup has not been run, try call setup on [' . static ::class . ']'));
				}
				if ($version === null) {
					end($this->upgrades);
					$version = key($this->upgrades);
				}
				if (isset($this->upgrades[$version]) === false) {
					throw new UpgradeException(sprintf('Requested unknown version [%s] for upgrade; there is no such upgrade available.', $version));
				}
				$upgrades = array_keys($this->upgrades);
				if (($current = $this->getVersion()) !== null && array_search($current, $upgrades, true) > array_search($version, $upgrades, true)) {
					throw new UpgradeException(sprintf('Current version [%s] is newer than requested version [%s] for upgrade.', $current, $version));
				}
				$upgrades = $current ? array_slice($this->upgrades, $index = array_search($current, array_keys($this->upgrades), true) + 1, null, true) : $this->upgrades;
				if (isset($index) && $index >= count($this->upgrades)) {
					throw new CurrentVersionException(sprintf('Version [%s] is current; no upgrades has been run.', $current));
				}
				$upgrade = null;
				foreach ($upgrades as $upgrade) {
					try {
						($last = $upgrade)->setup();
						$upgrade->onStart();
						$upgrade->upgrade();
						$upgrade->onSuccess();
						$this->onUpgrade($upgrade);
						if ($upgrade->getVersion() === $version) {
							break;
						}
					} catch (Throwable $throwable) {
						$upgrade->onFail($throwable);
					}
				}
				$this->onUpgradeEnd($upgrade);
				return $upgrade;
			} catch (Throwable $throwable) {
				$this->onUpgradeFailed($throwable, $last);
				throw $throwable;
			}
		}

		protected function onUpgradeStart(): void {
		}

		protected function onUpgrade(IUpgrade $upgrade): void {
		}

		protected function onUpgradeEnd(IUpgrade $upgrade): void {
		}

		protected function onUpgradeFailed(Throwable $throwable, IUpgrade $upgrade = null): void {
			$this->logService->exception($throwable);
		}
	}
