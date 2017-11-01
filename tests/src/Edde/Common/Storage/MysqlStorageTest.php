<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\IQueryBuilder;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Query\NativeQuery;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Driver\Database\Mysql\MysqlDriver;
		use Edde\Ext\Driver\Database\Mysql\MysqlQueryBuilder;

		class MysqlStorageTest extends AbstractStorageTest {
			public function testPrepareDatabase() {
				$this->storage->native(new NativeQuery('drop database edde'));
				$this->storage->native(new NativeQuery('create database edde'));
				$this->assertTrue(true, 'everything looks nice even here!');
			}

			/**
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			protected function setUp() {
				parent::setUp();
				ContainerFactory::inject($this, [
					IDriver::class       => ContainerFactory::instance(MysqlDriver::class, [
						'mysql:dbname=edde;host=172.17.0.1',
						'edde',
						'edde',
					]),
					IQueryBuilder::class => MysqlQueryBuilder::class,
					new ClassFactory(),
				]);
			}
		}
