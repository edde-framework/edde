<?php
	namespace Edde\Common\Database;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Database\IDriver;
		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\Exception\StorageException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Api\Storage\Inject\EntityManager;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Container\Factory\ClassFactory;
		use Edde\Common\Database\Driver\PostgresDriver;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Common\Query\SelectQuery;
		use Edde\Ext\Container\ContainerFactory;
		use Edde\Ext\Test\TestCase;
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
			 * @throws UnknownSchemaException
			 */
			public function testCreateSchema() {
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema(SimpleSchema::class)));
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
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'to-be-updated',
					'optional' => null,
					'value'    => 3.14,
					'date'     => new \DateTime('24.12.2020 12:24:13'),
					'question' => false,
				]);
				$this->storage->insert($entity);
				$entity->set('optional', 'this is a new nice and updated string');
				$expect = $entity->toArray();
				$this->storage->update($entity);
				$entity = $this->storage->load(SimpleSchema::class, (new SelectQuery())->table(SimpleSchema::class)->all()->where()->eq('guid')->to($entity->get('guid'))->query());
				self::assertEquals($expect, $array = $entity->toArray());
				self::assertTrue(gettype($array['value']) === 'double', 'value is not float!');
				self::assertInstanceOf(\DateTime::class, $array['date']);
				self::assertTrue(gettype($array['question']) === 'bool', 'question is not bool!');
				assertTrue($array['question']);
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
