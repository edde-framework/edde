<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\Asset\IAssetDirectory;
	use Edde\Api\Asset\IAssetStorage;
	use Edde\Api\Asset\IStorageDirectory;
	use Edde\Api\Database\IDriver;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Asset\AssetDirectory;
	use Edde\Common\Asset\AssetStorage;
	use Edde\Common\Asset\StorageDirectory;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\File;
	use Edde\Common\File\FileUtils;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Resource\ResourceList;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Upgrade\UpgradeManager;
	use Edde\Common\Url\Url;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Upgrade\InitialStorageUpgrade;
	use phpunit\framework\TestCase;

	class StyleSheetCompilerTest extends TestCase {
		/**
		 * @var IStyleSheetCompiler
		 */
		protected $styleSheetCompiler;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var IUpgradeManager
		 */
		protected $upgradeManager;
		/**
		 * @var SqliteDriver
		 */
		protected $sqliteDriver;

		public function setUp() {
			FileUtils::recreate(__DIR__ . '/public');
			$this->tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$this->tempDirectory->purge();

			$container = ContainerFactory::create([
				IStorage::class => DatabaseStorage::class,
				ISchemaFactory::class => SchemaFactory::class,
				ISchemaManager::class => SchemaManager::class,
				IUpgradeManager::class => UpgradeManager::class,
				IDriver::class => function () {
					return $this->sqliteDriver = new SqliteDriver('sqlite:' . $this->getDatabaseFileName());
				},
				IAssetStorage::class => AssetStorage::class,
				IRootDirectory::class => new RootDirectory(__DIR__),
				IAssetDirectory::class => new AssetDirectory(__DIR__ . '/public'),
				IStorageDirectory::class => new StorageDirectory(__DIR__ . '/public'),
				ITempDirectory::class => $this->tempDirectory,
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
			]);
			$this->styleSheetCompiler = $container->create(IStyleSheetCompiler::class);
			$this->storage = $container->create(IStorage::class);
			$this->schemaManager = $container->create(ISchemaManager::class);
			$this->upgradeManager = $container->create(IUpgradeManager::class);

			$this->upgradeManager->registerUpgrade($upgrade = new InitialStorageUpgrade());
			$container->inject($upgrade);
			$this->upgradeManager->upgrade();
		}

		protected function getDatabaseFileName() {
			return $this->tempDirectory->filename('resource-test-' . sha1(microtime() . random_int(0, 99999)) . '.sqlite');
		}

		public function testCommon() {
			$styleSheetCompiler = $this->styleSheetCompiler;
			$resourceList = new ResourceList();
			$resourceList->addResource(new File(__DIR__ . '/assets/css/font-awesome.css'));
			$resourceList->addResource(new File(__DIR__ . '/assets/css/font-awesome.min.css'));
			$resourceList->addResource(new File(__DIR__ . '/assets/css/simple-css.css'));
			$resourceList->addResource(new File(__DIR__ . '/assets/css/foundation.min.css'));

			$resource = $styleSheetCompiler->compile($resourceList);

			self::assertFileExists($resource->getUrl()
				->getAbsoluteUrl());
			$urlList = StringUtils::matchAll($resource->get(), "~url\\((?<url>['\"].*?['\"])\\)~", true);
			self::assertNotEmpty($urlList);
			self::assertArrayHasKey('url', $urlList);
			$count = 0;
			foreach (array_unique($urlList['url']) as $url) {
				$url = Url::create(str_replace([
					'"',
					"'",
				], null, $url));
				if (in_array($url->getScheme(), [
					'data',
				], true)) {
					continue;
				}
				$count++;
				self::assertFileExists(__DIR__ . '/' . $url->getPath());
			}
			self::assertEquals(6, $count);
		}

		protected function tearDown() {
			if ($this->sqliteDriver) {
				$this->sqliteDriver->close();
			}
			FileUtils::delete(__DIR__ . '/public');
			FileUtils::delete(__DIR__ . '/temp');
		}
	}
