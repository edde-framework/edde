<?php
	namespace Edde\Common\Database\Driver;

		use Edde\Api\Database\Exception\DriverQueryException;
		use Edde\Api\Database\Inject\Driver;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Common\Query\NativeQuery;
		use Edde\Ext\Container\ContainerFactory;

		class MysqlDriverTest extends AbstractDriverTest {
			use Driver;

			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testNativeQuery() {
				$this->driver->native(new NativeQuery('drop database edde'));
				$this->driver->native(new NativeQuery('create database edde'));
				$this->assertTrue(true, 'everything looks nice even here!');
			}

			protected function getDriverFactory() {
				return ContainerFactory::instance(MysqlDriver::class, [
					'mysql:dbname=edde;host=172.17.0.1',
					'edde',
					'edde',
				]);
			}
		}
