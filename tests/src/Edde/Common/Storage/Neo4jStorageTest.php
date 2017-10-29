<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Driver\IDriver;
		use Edde\Api\Query\IQueryBuilder;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Driver\Graph\Neo4j\Neo4jDriver;
		use Edde\Ext\Driver\Graph\Neo4j\Neo4jQueryBuilder;
		use Edde\Ext\Test\TestCase;
		use Edde\Test\FooSchema;
		use Edde\Test\SimpleSchema;

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
				$this->storage->createSchema(FooSchema::class);
			}

			public function testInsert() {
				$this->storage->push(FooSchema::class, [
					'name' => 'neo4j rocks!',
				]);
			}

			public function testInsertException() {
				$this->expectException(NullValueException::class);
				$this->storage->push(FooSchema::class, [
					'label' => 'kaboom',
				]);
			}

			public function testInsertException2() {
				$this->expectException(NullValueException::class);
				$this->storage->push(FooSchema::class, [
					'name'  => null,
					'label' => 'kaboom',
				]);
			}

			public function testInsertUnique() {
				$this->expectException(DuplicateEntryException::class);
				$this->storage->push(FooSchema::class, [
					'name' => 'unique',
				]);
				$this->storage->push(FooSchema::class, [
					'name' => 'unique',
				]);
			}

			/**
			 * @throws EntityNotFoundException
			 * @throws UnknownTableException
			 */
			public function testUpdate() {
				$entity = $this->storage->push(SimpleSchema::class, [
					'name'     => 'to-be-updated',
					'optional' => null,
					'value'    => 3.14,
					'date'     => new \DateTime('24.12.2020 12:24:13'),
					'question' => false,
				]);
				$entity->set('optional', 'this is a new nice and updated string');
				$expect = $entity->toArray();
				$this->storage->update($entity);
				$entity = $this->storage->collection(FooSchema::class)->load($entity->get('guid'));
				self::assertFalse($entity->isDirty(), 'entity should NOT be dirty right after load!');
				self::assertEquals($expect, $array = $entity->toArray());
				self::assertTrue(($type = gettype($array['value'])) === 'double', 'value [' . $type . '] is not float!');
				self::assertInstanceOf(\DateTime::class, $array['date']);
				self::assertTrue(($type = gettype($array['question'])) === 'boolean', 'question [' . $type . '] is not bool!');
				self::assertFalse($array['question']);
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
