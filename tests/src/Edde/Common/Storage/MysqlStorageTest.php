<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

	use Edde\Api\Driver\IDriver;
	use Edde\Common\Container\Factory\InstanceFactory;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Container\FactoryException;
	use Edde\Exception\Driver\DriverException;
	use Edde\Ext\Driver\MysqlDriver;
	use ReflectionException;

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

		/**
		 * @throws ContainerException
		 * @throws \Edde\Exception\Container\FactoryException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IDriver::class, MysqlDriver::class, [
				'mysql',
			]), IDriver::class);
		}
	}
