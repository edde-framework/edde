<?php
	namespace Edde\Common\Storage;

		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\DuplicateTableException;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\StorageException;
		use Edde\Api\Storage\Exception\UnknownTableException;
		use Edde\Api\Storage\Inject\EntityManager;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Ext\Test\TestCase;
		use Edde\Test\BarSchema;
		use Edde\Test\FooBarSchema;
		use Edde\Test\FooSchema;
		use Edde\Test\SimpleSchema;

		abstract class AbstractStorageTest extends TestCase {
			use Storage;
			use EntityManager;

			/**
			 * @throws DuplicateTableException
			 */
			public function testCreateSchema() {
				$this->storage->createSchema(SimpleSchema::class);
				$this->storage->createSchema(FooSchema::class);
				$this->storage->createSchema(BarSchema::class);
				$this->storage->createSchema(FooBarSchema::class);
				self::assertTrue(true, 'everything is ok');
			}

			/**
			 * @throws DuplicateEntryException
			 */
			public function testInsert() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'this entity is new',
					'optional' => 'foo-bar',
				]);
				$this->storage->push(FooSchema::class, [
					'name' => 'neo4j rocks!',
				]);
				$this->storage->insert($entity);
				self::assertNotEmpty($entity->get('guid'));
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
			 * @throws StorageException
			 */
			public function testRelation() {
				$foo = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo The First',
				]);
				$bar = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Second',
				]);
				$this->storage->save($this->entityManager->attach($foo, $bar, FooBarSchema::class));
				/**
				 * second save of the same entities will survive, because save is checking presence by primary
				 * keys and FooBarSchema is using it's properties as a primary key
				 */
				$this->storage->save($this->entityManager->attach($foo, $bar, FooBarSchema::class));
				$exception = null;
				try {
					/**
					 * this will fail, because insert is bypassing storage check of entity presence
					 */
					$this->storage->relation($this->entityManager->attach($foo, $bar, FooBarSchema::class));
				} catch (DuplicateEntryException $exception) {
				}
				self::assertNotEmpty($exception, 'insert has not thrown an exception!');
			}
		}
