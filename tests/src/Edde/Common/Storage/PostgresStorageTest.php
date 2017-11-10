<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Driver\PostgresDriver;

		class PostgresStorageTest extends AbstractStorageTest {
			public function testPrepareDatabase() {
				$this->storage->native('drop schema if exists test cascade');
				$this->storage->native('create schema test authorization "edde"');
				$this->assertTrue(true, 'everything is OK!');
			}

			/**
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			protected function setUp() {
				ContainerFactory::inject($this, [
					IDriver::class => ContainerFactory::instance(PostgresDriver::class, ['pgsql:dbname=edde;user=edde;password=edde;host=172.17.0.1']),
					new ClassFactory(),
				]);
				$this->storage->native('set search_path to "test"');
			}

			protected function getEntityTimeLimit(): float {
				return 12;
			}

			protected function getBenchmarkLimit(): int {
				return 500;
			}
		}
