<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Container\Factory\InstanceFactory;

	class Neo4jStorageTest extends AbstractStorageTest {
		/**
		 * @throws StorageException
		 */
		public function testPrepareDatabase() {
			$this->storage->exec('MATCH (n) DETACH DELETE n');
			self::assertTrue(true, 'everything is ok, yapee!');
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InstanceFactory(IStorage::class, Neo4jStorage::class));
		}
	}
