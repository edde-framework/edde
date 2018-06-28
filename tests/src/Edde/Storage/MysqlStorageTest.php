<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Container\ContainerException;
	use Edde\Factory\InstanceFactory;
	use Edde\Schema\SchemaException;
	use Edde\Service\Container\Container;
	use Edde\Service\Storage\Storage;
	use ReflectionException;

	class MysqlStorageTest extends AbstractStorageTest {
		use Container;
		use Storage;

		/**
		 * @throws StorageException
		 */
		public function testPrepareDatabase() {
			$this->storage->exec('DROP DATABASE `edde`');
			$this->storage->exec('CREATE DATABASE `edde`');
			$this->storage->exec('USE `edde`');
			$this->assertTrue(true, 'everything looks nice even here!');
		}

		/**
		 * @throws ContainerException
		 * @throws ReflectionException
		 * @throws SchemaException
		 */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IStorage::class, MysqlStorage::class));
		}
	}
