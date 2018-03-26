<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Connection\ConnectionException;
	use Edde\Connection\IConnection;
	use Edde\Connection\PostgresConnection;
	use Edde\Container\ContainerException;
	use Edde\Container\Factory\InstanceFactory;
	use ReflectionException;

	class PostgresStorageTest extends AbstractStorageTest {
		/**
		 * @throws ConnectionException
		 */
		public function testPrepareDatabase() {
			$this->connection->exec('DROP SCHEMA IF EXISTS "test" CASCADE');
			$this->connection->exec('CREATE SCHEMA "test" AUTHORIZATION "edde"');
			$this->assertTrue(true, 'everything is OK!');
		}

		/**
		 * @inheritdoc
		 *
		 * @throws ConnectionException
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IConnection::class, PostgresConnection::class), IConnection::class);
			$this->connection->fetch('SET SEARCH_PATH TO "test"');
		}
	}
