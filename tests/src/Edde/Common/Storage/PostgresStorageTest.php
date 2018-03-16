<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Driver\Exception\DriverException;
	use Edde\Api\Driver\IDriver;
	use Edde\Common\Container\Factory\InstanceFactory;
	use Edde\Ext\Driver\PostgresDriver;

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
		 * @throws ContainerException
		 * @throws DriverException
		 */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IDriver::class, PostgresDriver::class, [
				'postgres',
			]), IDriver::class);
			$this->storage->fetch('SET SEARCH_PATH TO "test"');
		}

		/**
		 * @throws DriverException
		 */
		protected function beforeBenchmark() {
			$this->storage->exec('DROP SCHEMA IF EXISTS "test" CASCADE');
			$this->storage->exec('CREATE SCHEMA "test" AUTHORIZATION "edde"');
		}

		protected function getEntityTimeLimit(): float {
			return 40;
		}

		protected function getBenchmarkLimit(): int {
			return 500;
		}
	}
