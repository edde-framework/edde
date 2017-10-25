<?php
	namespace Edde\Common\Database;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Database\IDriver;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\Exception\StorageException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Api\Storage\Inject\EntityManager;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Database\Driver\PostgresDriver;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Test\TestCase;
		use Edde\Test\BarSchema;
		use Edde\Test\FooBarSchema;
		use Edde\Test\FooSchema;
		use Edde\Test\SimpleSchema;

		class DatabaseStorageTest extends TestCase {
			use SchemaManager;
			use EntityManager;
			use Storage;

			public function testPrepareDatabase() {
				$this->storage->query('DROP SCHEMA IF EXISTS "test" CASCADE');
				$this->storage->query('CREATE SCHEMA "test" AUTHORIZATION "edde"');
				$this->assertTrue(true, 'everything is OK!');
			}

			/**
			 * @throws DuplicateTableException
			 */
			public function testCreateSchema() {
				$this->storage->createSchema(SimpleSchema::class);
				self::assertTrue(true, 'everything is ok');
			}

			/**
			 * @throws IntegrityException
			 * @throws StorageException
			 */
			public function testSave() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'some name for this entity',
					'optional' => 'this string is optional, but I wanna fill it!',
				]);
				$this->storage->save($entity);
				self::assertNotEmpty($entity->get('guid'));
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'another name',
					'optional' => null,
				]);
				$this->storage->save($entity);
				self::assertFalse($entity->isDirty(), 'Entity is still dirty!');
				self::assertNotEmpty($entity->get('guid'));
			}

			public function testInsert() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'this entity is new',
					'optional' => 'foo-bar',
				]);
				$this->storage->insert($entity);
				self::assertNotEmpty($entity->get('guid'));
			}

			/**
			 * @throws EntityNotFoundException
			 * @throws UnknownTableException
			 * @throws \Exception
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
				$entity = $this->storage->collection(SimpleSchema::class)->load($entity->get('guid'));
				self::assertFalse($entity->isDirty(), 'entity should NOT be dirty right after load!');
				self::assertEquals($expect, $array = $entity->toArray());
				self::assertTrue(($type = gettype($array['value'])) === 'double', 'value [' . $type . '] is not float!');
				self::assertInstanceOf(\DateTime::class, $array['date']);
				self::assertTrue(($type = gettype($array['question'])) === 'boolean', 'question [' . $type . '] is not bool!');
				self::assertFalse($array['question']);
			}

			/**
			 * @throws DuplicateTableException
			 * @throws IntegrityException
			 * @throws StorageException
			 */
			public function testRelation() {
				/**
				 * there is an idea to have kind of links in schema reflection (thus it will be
				 * possible to describe a bit more complex data structures by a reflection)
				 */
				$this->storage->createSchema(FooSchema::class);
				$this->storage->createSchema(BarSchema::class);
				$this->storage->createSchema(FooBarSchema::class);
				$foo = $this->storage->push(FooSchema::class, [
					'name' => 'foo The First',
				]);
				$bar = $this->storage->push(BarSchema::class, [
					'name' => 'bar The Second',
				]);
				$this->storage->save($this->entityManager->attach($foo, $bar, FooBarSchema::class));
				$this->storage->save($this->entityManager->attach($bar, $foo, FooBarSchema::class));
			}

			public function testMultiUnique() {
			}

			/**
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			protected function setUp() {
				ContainerFactory::inject($this, [
					IDriver::class => ContainerFactory::instance(PostgresDriver::class, ['pgsql:dbname=edde;user=edde;password=edde;host=172.17.0.1']),
					new ClassFactory(),
				]);
				$this->storage->query('SET search_path TO "test"');
			}
		}
