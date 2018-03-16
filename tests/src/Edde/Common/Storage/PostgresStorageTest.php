<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

	use Edde\Api\Driver\IDriver;
	use Edde\Common\Container\Factory\InstanceFactory;
	use Edde\Driver\PostgresDriver;
	use Edde\Exception\Driver\DriverException;
	use ReflectionException;

	class PostgresStorageTest extends AbstractStorageTest {
		/**
		 * @throws DriverException
		 */
		public function testPrepareDatabase() {
			$this->storage->exec('DROP SCHEMA IF EXISTS "test" CASCADE');
			$this->storage->exec('CREATE SCHEMA "test" AUTHORIZATION "edde"');
			$this->assertTrue(true, 'everything is OK!');
		}

		/**
		 * @throws \Edde\Exception\Container\ContainerException
		 * @throws \Edde\Exception\Driver\DriverException
		 * @throws \Edde\Exception\Container\FactoryException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IDriver::class, PostgresDriver::class, [
				'postgres',
			]), IDriver::class);
			$this->storage->fetch('SET SEARCH_PATH TO "test"');
		}
	}
