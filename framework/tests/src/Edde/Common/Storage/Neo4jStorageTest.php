<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Driver\IDriver;
	use Edde\Api\Storage\Inject\Storage;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Driver\Neo4jDriver;
	use function explode;
	use function getenv;

	class Neo4jStorageTest extends AbstractStorageTest {
		use Storage;

		public function testPrepareDatabase() {
			$this->storage->native('MATCH (n) DETACH DELETE n');
			self::assertTrue(true, 'everything is ok, yapee!');
		}

		/**
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		protected function setUp() {
			ContainerFactory::inject($this, [
				IDriver::class => ContainerFactory::instance(Neo4jDriver::class, [
					'bolt://neo4j:7687',
					explode('/', getenv('NEO4J_AUTH')),
				]),
				new ClassFactory(),
			]);
		}

		protected function getEntityTimeLimit(): float {
			return 130;
		}
	}
