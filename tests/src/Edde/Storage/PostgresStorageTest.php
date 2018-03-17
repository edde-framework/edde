<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Container\Factory\InstanceFactory;
	use Edde\Driver\DriverException;
	use Edde\Driver\IDriver;
	use Edde\Driver\PostgresDriver;

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
		 * @inheritdoc
		 *
		 * @throws DriverException
		 */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IDriver::class, PostgresDriver::class), IDriver::class);
			$this->storage->fetch('SET SEARCH_PATH TO "test"');
		}
	}
