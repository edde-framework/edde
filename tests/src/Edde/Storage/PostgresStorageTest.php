<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Container\ContainerException;
	use Edde\Container\Factory\InterfaceFactory;
	use Edde\Schema\SchemaException;
	use Edde\Service\Hydrator\HydratorManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Security\RandomService;
	use Edde\Service\Storage\Storage;
	use ReflectionException;

	class PostgresStorageTest extends AbstractStorageTest {
		use Storage;
		use SchemaManager;
		use RandomService;
		use HydratorManager;

		/**
		 * @throws StorageException
		 */
		public function testPrepareDatabase() {
			$this->storage->exec('DROP SCHEMA IF EXISTS "test" CASCADE');
			$this->storage->exec('CREATE SCHEMA "test" AUTHORIZATION "edde"');
			$this->assertTrue(true, 'everything is OK!');
		}

		/**
		 * @inheritdoc
		 *
		 * @throws ContainerException
		 * @throws ReflectionException
		 * @throws SchemaException
		 * @throws StorageException
		 */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InterfaceFactory(IStorage::class, PostgresStorage::class));
			$this->storage->exec('SET "search_path" TO "test"');
		}
	}
