<?php
	declare(strict_types=1);
	namespace Edde\Access;

	use Edde\Connection\IConnection;
	use Edde\Connection\Neo4jConnection;
	use Edde\Container\Factory\InstanceFactory;
	use Edde\TestCase;

	class AccessServiceTest extends TestCase {
		public function testAccessList() {
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IConnection::class, Neo4jConnection::class), IConnection::class);
		}
	}
