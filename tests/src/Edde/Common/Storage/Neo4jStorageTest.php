<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\IQueryBuilder;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Driver\Graph\Neo4j\Neo4jDriver;
		use Edde\Ext\Driver\Graph\Neo4j\Neo4jQueryBuilder;
		use Edde\Ext\Test\TestCase;
		use Edde\Test\FooSchema;

		class Neo4jStorageTest extends TestCase {
			use Storage;

			public function testPrepareDatabase() {
				$this->storage->query('MATCH (n) DETACH DELETE n');
				self::assertTrue(true, 'everything is ok, yapee!');
			}

			/**
			 * @throws DuplicateTableException
			 */
			public function testCreateSchema() {
				$this->storage->start();
				$this->storage->createSchema(FooSchema::class);
				$this->storage->commit();
			}

			public function testInsert() {
				$this->storage->push(FooSchema::class, [
					'name' => 'neo4j rocks!',
				]);
			}

			/**
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			protected function setUp() {
				ContainerFactory::inject($this, [
					IDriver::class       => ContainerFactory::instance(Neo4jDriver::class, ['bolt://172.17.0.1']),
					IQueryBuilder::class => Neo4jQueryBuilder::class,
					new ClassFactory(),
				]);
			}
		}
