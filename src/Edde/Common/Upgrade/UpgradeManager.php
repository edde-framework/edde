<?php
	declare(strict_types = 1);

	namespace Edde\Common\Upgrade;

	use Edde\Api\Storage\LazyStorageTrait;
	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Upgrade\UpgradeException;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Event\EventTrait;
	use Edde\Common\Upgrade\Event\OnUpgradeEvent;
	use Edde\Common\Upgrade\Event\UpgradeEndEvent;
	use Edde\Common\Upgrade\Event\UpgradeFailEvent;
	use Edde\Common\Upgrade\Event\UpgradeStartEvent;

	/**
	 * Default implementation of a upgrade manager.
	 */
	class UpgradeManager extends AbstractDeffered implements IUpgradeManager {
		use EventTrait;
		use LazyStorageTrait;
		/**
		 * @var IUpgrade[]
		 */
		protected $upgradeList = [];
		/**
		 * @var string
		 */
		protected $currentVersion;

		/**
		 * @inheritdoc
		 * @throws UpgradeException
		 */
		public function registerUpgrade(IUpgrade $upgrade, bool $force = false): IUpgradeManager {
			$version = $upgrade->getVersion();
			if ($force === false && isset($this->upgradeList[$version])) {
				throw new UpgradeException(sprintf('Cannot register upgrade [%s] with version [%s] - version is already present.', get_class($upgrade), $version));
			}
			$this->upgradeList[$upgrade->getVersion()] = $upgrade;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws UpgradeException
		 */
		public function setCurrentVersion(string $currentVersion = null): IUpgradeManager {
			$this->use();
			if ($currentVersion && isset($this->upgradeList[$currentVersion]) === false) {
				throw new UpgradeException(sprintf('Setting unknown current version [%s].', $currentVersion));
			}
			$this->currentVersion = $currentVersion;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getUpgradeList(): array {
			$this->use();
			return $this->upgradeList;
		}

		/**
		 * @inheritdoc
		 * @throws \Exception
		 */
		public function upgrade(): IUpgrade {
			return $this->upgradeTo();
		}

		/**
		 * @inheritdoc
		 * @throws \Exception
		 */
		public function upgradeTo(string $version = null): IUpgrade {
			$this->use();
			if ($version === null) {
				end($this->upgradeList);
				$version = key($this->upgradeList);
			}
			if ($version === null) {
				throw new UpgradeException('Cannot run upgrade - there are no upgrades.');
			}
			if (isset($this->upgradeList[$version]) === false) {
				throw new UpgradeException(sprintf('Cannot run upgrade - unknown upgrade version [%s].', $version));
			}
			$last = null;
			try {
				$this->storage->start();
				$this->event(new UpgradeStartEvent($this));
				$upgradeList = $this->currentVersion ? array_slice($this->upgradeList, $index = array_search($this->currentVersion, array_keys($this->upgradeList), true) + 1, null, true) : $this->upgradeList;
				if (isset($index) && $index >= count($this->upgradeList)) {
					throw new CurrentVersionException(sprintf('Version [%s] is current; no upgrades has been run.', $this->currentVersion));
				}
				foreach ($upgradeList as $upgrade) {
					$this->event($onUpgradeEvent = new OnUpgradeEvent($this, $upgrade));
					if ($onUpgradeEvent->isSuppressed()) {
						continue;
					}
					$last = $upgrade;
					$upgrade->upgrade();
					if ($upgrade->getVersion() === $version) {
						break;
					}
				}
				$this->event(new UpgradeEndEvent($this));
				$this->storage->commit();
			} catch (\Exception $e) {
				if ($last) {
					$this->event(new UpgradeFailEvent($this, $last));
				}
				$this->storage->rollback();
				throw $e;
			}
			if ($last === null) {
				throw new UpgradeException(sprintf('No upgrades has been run for version [%s].', $version));
			}
			return $last;
		}
	}
