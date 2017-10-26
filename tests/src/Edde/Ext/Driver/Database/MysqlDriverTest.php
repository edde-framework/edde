<?php
	namespace Edde\Ext\Driver\Database;

		use Edde\Api\Driver\Exception\DriverQueryException;
		use Edde\Api\Driver\Inject\Driver;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Common\Query\NativeQuery;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Driver\Database\Mysql\MysqlDriver;
		use Edde\Ext\Driver\Database\Mysql\MysqlQueryBuilder;

		class MysqlDriverTest extends AbstractDriverTest {
			use Driver;

			/**
			 * @throws DriverQueryException
			 * @throws IntegrityException
			 */
			public function testNativeQuery() {
				$this->driver->execute(new NativeQuery('drop database edde'));
				$this->driver->execute(new NativeQuery('create database edde'));
				$this->assertTrue(true, 'everything looks nice even here!');
			}

			protected function getDriverFactory() {
				return ContainerFactory::instance(MysqlDriver::class, [
					'mysql:dbname=edde;host=172.17.0.1',
					'edde',
					'edde',
				]);
			}

			protected function getQueryBuilderFactory() {
				return MysqlQueryBuilder::class;
			}
		}
