<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Driver\Exception\DriverException;
	use Edde\Api\Driver\IDriver;
	use Edde\Common\Container\Factory\InstanceFactory;
	use Edde\Ext\Driver\Neo4jDriver;

	class Neo4jStorageTest extends AbstractStorageTest {
		/**
		 * @throws DriverException
		 */
		public function testPrepareDatabase() {
			$this->storage->exec('MATCH (n) DETACH DELETE n');
			self::assertTrue(true, 'everything is ok, yapee!');
		}

		/**
		 * @throws ContainerException
		 */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IDriver::class, Neo4jDriver::class, [
				'neo4j',
			]), IDriver::class);
		}

		protected function getEntityTimeLimit(): float {
			return 130;
		}
	}
