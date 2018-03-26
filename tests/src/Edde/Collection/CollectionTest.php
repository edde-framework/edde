<?php
	declare(strict_types=1);
	namespace Edde\Collection;

	use Edde\Connection\IConnection;
	use Edde\Connection\Neo4jConnection;
	use Edde\Container\Factory\InstanceFactory;
	use Edde\Service\Connection\Connection;
	use Edde\Service\Transaction\Transaction;
	use Edde\TestCase;

	class CollectionTest extends TestCase {
		use Transaction;
		use Connection;

		public function testCreateSchema() {
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IConnection::class, Neo4jConnection::class), IConnection::class);
			$this->connection->execute();
		}
	}
