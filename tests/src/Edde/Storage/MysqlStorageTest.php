<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Connection\ConnectionException;
	use Edde\Connection\IConnection;
	use Edde\Connection\MysqlConnection;
	use Edde\Container\Factory\InstanceFactory;

	class MysqlStorageTest extends AbstractStorageTest {
		/**
		 * @throws ConnectionException
		 */
		public function testPrepareDatabase() {
			$this->connection->exec('DROP DATABASE `edde`');
			$this->connection->exec('CREATE DATABASE `edde`');
			$this->connection->exec('USE `edde`');
			$this->assertTrue(true, 'everything looks nice even here!');
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IConnection::class, MysqlConnection::class), IConnection::class);
		}
	}
