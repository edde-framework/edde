<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

	use Edde\Common\Container\Factory\InstanceFactory;
	use Edde\Driver\DriverException;
	use Edde\Driver\IDriver;
	use Edde\Driver\MysqlDriver;

	class MysqlStorageTest extends AbstractStorageTest {
		/**
		 * @throws DriverException
		 */
		public function testPrepareDatabase() {
			$this->storage->exec('DROP DATABASE `edde`');
			$this->storage->exec('CREATE DATABASE `edde`');
			$this->storage->exec('USE `edde`');
			$this->assertTrue(true, 'everything looks nice even here!');
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IDriver::class, MysqlDriver::class, [
				'mysql',
			]), IDriver::class);
		}
	}
