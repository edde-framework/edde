<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Connection\ConnectionException;
	use Edde\Connection\IConnection;
	use Edde\Connection\Neo4jConnection;
	use Edde\Container\Factory\InstanceFactory;

	class Neo4jStorageTest extends AbstractStorageTest {
		/**
		 * @throws ConnectionException
		 */
		public function testPrepareDatabase() {
			$this->connection->exec('MATCH (n) DETACH DELETE n');
			self::assertTrue(true, 'everything is ok, yapee!');
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IConnection::class, Neo4jConnection::class), IConnection::class);
		}
	}
