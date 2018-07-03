<?php
	declare(strict_types=1);

	namespace Edde\Common\Upgrade;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\LazyCrateFactoryTrait;
	use Edde\Api\Schema\LazySchemaManagerTrait;
	use Edde\Api\Storage\LazyStorageTrait;
	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Upgrade\IUpgradeStorable;
	use Edde\Api\Upgrade\UpgradeException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;
	use Edde\Common\Query\Schema\CreateSchemaQuery;

	/**
	 * Default implementation of a upgrade manager.
	 */
	abstract class AbstractUpgradeManager extends Object implements IUpgradeManager {
		use LazyStorageTrait;
		use LazySchemaManagerTrait;
		use LazyCrateFactoryTrait;
		use ConfigurableTrait;
		/**
		 * @var IUpgrade[]
		 */
		protected $upgradeList = [];

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
		 */
		public function createUpgradeStorable(array $load = null): ICrate {
			return $this->crateFactory->crate(IUpgradeStorable::class, $load);
		}

		/**
		 * @inheritdoc
		 */
		public function getUpgradeList(): array {
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
				$this->onUpgradeStart();
				$current = $this->getCurrentVersion();
				/** @var $upgradeList IUpgrade[] */
				$upgradeList = $current ? array_slice($this->upgradeList, $index = array_search($current, array_keys($this->upgradeList), true) + 1, null, true) : $this->upgradeList;
				if (isset($index) && $index >= count($this->upgradeList)) {
					throw new CurrentVersionException(sprintf('Version [%s] is current; no upgrades has been run.', $current));
				}
				foreach ($upgradeList as $upgrade) {
					$last = $upgrade;
					$this->onUpgrade($upgrade);
					$upgrade->upgrade();
					if ($upgrade->getVersion() === $version) {
						break;
					}
				}
				$this->onUpgradeEnd();
			} catch (CurrentVersionException $exception) {
				throw $exception;
			} catch (\Exception $exception) {
				$this->onUpgradeFailed($exception, $last);
			}
			if ($last === null) {
				throw new UpgradeException(sprintf('No upgrades has been run for version [%s].', $version));
			}
			return $last;
		}

		protected function onUpgradeStart() {
			$this->schemaManager->setup();
			$this->storage->setup();
			$this->storage->start();
			$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema(IUpgradeStorable::class)));
		}

		protected function onUpgrade(IUpgrade $upgrade) {
		}

		protected function onUpgradeEnd() {
			$this->storage->commit();
		}

		protected function onUpgradeFailed(\Exception $exception, IUpgrade $last = null) {
			$this->storage->rollback();
			throw $exception;
		}
	}
