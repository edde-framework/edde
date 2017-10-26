<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Storage\Inject\Storage;
		use Edde\Api\Storage\IStorage;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Storage\Neo4jStorage;
		use Edde\Ext\Test\TestCase;

		class Neo4jStorageTest extends TestCase {
			use Storage;

			public function testPrepareDatabase() {
			}

			protected function setUp() {
				ContainerFactory::inject($this, [
					IStorage::class => ContainerFactory::instance(Neo4jStorage::class, []),
					new ClassFactory(),
				]);
			}
		}
