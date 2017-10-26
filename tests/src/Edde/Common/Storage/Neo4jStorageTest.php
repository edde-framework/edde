<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Query\NativeQuery;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Driver\Graph\Neo4j\Neo4jDriver;
		use Edde\Ext\Test\TestCase;

		class Neo4jStorageTest extends TestCase {
			use Storage;

			public function testPrepareDatabase() {
				$this->storage->native(new NativeQuery('MATCH (n) DETACH DELETE n'));
				self::assertTrue(true, 'everything is ok, yapee!');
			}

			/**
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			protected function setUp() {
				ContainerFactory::inject($this, [
					IDriver::class => ContainerFactory::instance(Neo4jDriver::class, ['bolt://172.17.0.1']),
					new ClassFactory(),
				]);
			}
		}
