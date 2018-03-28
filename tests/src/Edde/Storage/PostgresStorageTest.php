<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Container\ContainerException;
	use Edde\Container\Factory\InstanceFactory;
	use ReflectionException;

	class PostgresStorageTest extends AbstractStorageTest {
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
		 * @throws StorageException
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IStorage::class, PostgresStorage::class), IStorage::class);
			$this->storage->fetch('SET SEARCH_PATH TO "test"');
		}
	}
