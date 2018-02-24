<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Driver\IDriver;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Driver\MysqlDriver;
	use function getenv;

	class MysqlStorageTest extends AbstractStorageTest {
		public function testPrepareDatabase() {
			$this->storage->native('drop database edde');
			$this->storage->native('create database edde');
			$this->assertTrue(true, 'everything looks nice even here!');
		}

		/**
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		protected function setUp() {
			parent::setUp();
			ContainerFactory::inject($this, [
				IDriver::class => ContainerFactory::instance(MysqlDriver::class, [
					'mysql:dbname=edde;host=mysql;port=3306',
					'edde',
					getenv('MYSQL_PASSWORD'),
				]),
				new ClassFactory(),
			]);
		}

		protected function beforeBenchmark() {
			$this->storage->native('drop database edde');
			$this->storage->native('create database edde');
		}

		protected function getEntityTimeLimit(): float {
			return 30;
		}

		protected function getBenchmarkLimit(): int {
			return 500;
		}
	}
