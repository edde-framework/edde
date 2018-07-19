<?php
	declare(strict_types=1);
	namespace Edde\Upgrade;

	use Edde\Container\ContainerException;
	use Edde\Factory\InterfaceFactory;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Upgrade\UpgradeManager;
	use Edde\Service\Upgrade\VersionService;
	use Edde\Storage\UnknownTableException;
	use Edde\TestCase;
	use TestUpgradeSchema;
	use UpgradeConfigurator;
	use VersionTestService;

	class UpgradeManagerTest extends TestCase {
		use UpgradeManager;
		use VersionService;
		use SchemaManager;
		use Storage;

		/**
		 * @throws CurrentVersionException
		 * @throws UpgradeException
		 */
		public function testNoUpgrades() {
			$this->expectException(UpgradeException::class);
			$this->expectExceptionMessage('Cannot run upgrade: there are no available upgrades. Upgrade manager is not probably properly configured.');
			$this->upgradeManager->upgrade();
		}

		/**
		 * @throws CurrentVersionException
		 * @throws UpgradeException
		 * @throws ContainerException
		 */
		public function testUnknownUpgrade() {
			$this->expectException(UpgradeException::class);
			$this->expectExceptionMessage('Requested unknown version [3.0] for upgrade; there is no such upgrade available.');
			$this->container->registerConfigurator(IUpgradeManager::class, $this->container->inject(new UpgradeConfigurator()));
			$this->upgradeManager->upgrade('3.0');
		}

		/**
		 * @throws CurrentVersionException
		 * @throws UpgradeException
		 * @throws ContainerException
		 */
		public function testUpgrade() {
			$this->container->registerConfigurator(IUpgradeManager::class, $this->container->inject(new UpgradeConfigurator()));
			self::assertNull($this->versionService->getVersion());
			$upgrade = $this->upgradeManager->upgrade();
			self::assertSame('2.0', $upgrade->getVersion());
			self::assertSame('2.0', $this->versionService->getVersion());
		}

		/**
		 * @throws CurrentVersionException
		 * @throws UpgradeException
		 * @throws ContainerException
		 */
		public function testRollingUpgrade() {
			$this->container->registerConfigurator(IUpgradeManager::class, $this->container->inject(new UpgradeConfigurator()));
			self::assertNull($this->versionService->getVersion());
			$this->upgradeManager->upgrade('1.0');
			$this->upgradeManager->upgrade('1.5');
			$this->upgradeManager->upgrade('2.0');
			self::assertSame('2.0', $this->versionService->getVersion());
		}

		/**
		 * @throws CurrentVersionException
		 * @throws UpgradeException
		 * @throws ContainerException
		 */
		public function testNewerUpgradeException() {
			$this->expectException(UpgradeException::class);
			$this->expectExceptionMessage('Current version [2.0] is newer than requested version [1.5] for upgrade.');
			$this->container->registerConfigurator(IUpgradeManager::class, $this->container->inject(new UpgradeConfigurator()));
			self::assertNull($this->versionService->getVersion());
			$this->upgradeManager->upgrade();
			$this->upgradeManager->upgrade('1.5');
		}

		/**
		 * @throws CurrentVersionException
		 * @throws UpgradeException
		 * @throws ContainerException
		 */
		public function testCurrentVersionException() {
			$this->expectException(UpgradeException::class);
			$this->expectExceptionMessage('Version [2.0] is current; no upgrades has been run.');
			$this->container->registerConfigurator(IUpgradeManager::class, $this->container->inject(new UpgradeConfigurator()));
			self::assertNull($this->versionService->getVersion());
			$this->upgradeManager->upgrade();
			$this->upgradeManager->upgrade();
		}

		protected function setUp() {
			parent::setUp();
			$this->schemaManager->load(TestUpgradeSchema::class);
			try {
				$this->storage->exec($this->storage->query('DROP TABLE u:schema', ['u' => TestUpgradeSchema::class]));
			} catch (UnknownTableException $_) {
			}
			$this->container->registerFactory(new InterfaceFactory(IVersionService::class, VersionTestService::class));
		}
	}
