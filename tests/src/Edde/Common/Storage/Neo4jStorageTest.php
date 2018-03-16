<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

	use Edde\Common\Container\Factory\InstanceFactory;
	use Edde\Driver\IDriver;
	use Edde\Driver\Neo4jDriver;

	class Neo4jStorageTest extends AbstractStorageTest {
		/**
		 * @throws \Edde\Driver\DriverException
		 */
		public function testPrepareDatabase() {
			$this->storage->exec('MATCH (n) DETACH DELETE n');
			self::assertTrue(true, 'everything is ok, yapee!');
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IDriver::class, Neo4jDriver::class, [
				'neo4j',
			]), IDriver::class);
		}
	}
