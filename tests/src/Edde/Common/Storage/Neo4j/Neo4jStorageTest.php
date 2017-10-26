<?php
	namespace Edde\Common\Storage\Neo4j;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Query\NativeQuery;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Storage\Neo4j\Neo4jStorage;
		use Edde\Ext\Test\TestCase;

		class Neo4jStorageTest extends TestCase {
			use Storage;

			public function testPrepareDatabase() {
				$this->storage->native(new NativeQuery('MATCH (n) DETACH DELETE n'));
			}

			/**
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			protected function setUp() {
				ContainerFactory::inject($this, [
					IStorage::class => ContainerFactory::instance(Neo4jStorage::class, []),
					new ClassFactory(),
				]);
			}
		}
