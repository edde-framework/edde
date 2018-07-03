<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Crate\ICrateDirectory;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Crate\ICrateLoader;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Cache\DummyCacheManager;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\Header2Schema;
	use Foo\Bar\Item2Schema;
	use Foo\Bar\Row2Schema;
	use phpunit\framework\TestCase;

	require_once __DIR__ . '/assets/schema.php';

	/**
	 * Crate generator related tests.
	 */
	class CrateGeneratorTest extends TestCase {
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var ICrateGenerator
		 */
		protected $crateGenerator;
		/**
		 * @var ITempDirectory
		 */
		protected $crateDirectory;

		public function testCommon() {
			foreach ($this->schemaManager->getSchemaList() as $schema) {
				$name = $schema->getSchemaName();
				self::assertTrue(class_exists($name));
				$reflectionClass = new \ReflectionClass($name);
				/** @noinspection ForeachSourceInspection */
				foreach ($schema->getMeta('implements', []) as $meta) {
					self::assertContains($meta, $reflectionClass->getInterfaceNames());
				}
			}
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				ISchemaManager::class => SchemaManager::class,
				ISchemaFactory::class => SchemaFactory::class,
				ICrateGenerator::class => CrateGenerator::class,
				ICrateDirectory::class => function () {
					return new CrateDirectory(__DIR__ . '/temp/crate');
				},
				ITempDirectory::class => function () {
					return new TempDirectory(__DIR__ . '/temp');
				},
				ICacheManager::class => new DummyCacheManager(),
				ICrateLoader::class => CrateLoader::class,
			]);
			$this->schemaManager = $container->create(ISchemaManager::class);
			$this->schemaManager->addSchema($header = new Header2Schema());
			$this->schemaManager->addSchema($item = new Item2Schema());
			$this->schemaManager->addSchema(new Row2Schema($header, $item));
			$this->crateGenerator = $container->create(ICrateGenerator::class);
			$this->crateDirectory = $container->create(ICrateDirectory::class);
			$this->crateDirectory->purge();
			spl_autoload_register($container->create(ICrateLoader::class));
		}

		protected function tearDown() {
			$this->crateDirectory->delete();
		}
	}
