<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\StorageException;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Common\Schema\BarSchema;
		use Edde\Common\Schema\FooBarSchema;
		use Edde\Common\Schema\FooSchema;
		use Edde\Common\Schema\SimpleSchema;
		use Edde\Ext\Test\TestCase;

		abstract class AbstractStorageTest extends TestCase {
			use EntityManager;
			use SchemaManager;
			use Storage;

			/**
			 * @throws UnknownSchemaException
			 */
			public function testCreateSchema() {
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(SimpleSchema::class)));
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(FooSchema::class)));
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(BarSchema::class)));
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(FooBarSchema::class)));
				self::assertTrue(true, 'everything is ok');
			}

			/**
			 * @throws DuplicateEntryException
			 * @throws IntegrityException
			 * @throws StorageException
			 */
			public function testInsert() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'this entity is new',
					'optional' => 'foo-bar',
				]);
				self::assertNotEmpty($entity->get('guid'));
				$entity->save();
				self::assertFalse($entity->isDirty());
			}

			/**
			 * @throws DuplicateEntryException
			 * @throws IntegrityException
			 * @throws StorageException
			 */
			public function testInsertException() {
				$this->expectException(NullValueException::class);
				$this->entityManager->create(FooSchema::class, [
					'label' => 'kaboom',
				])->save();
			}

			/**
			 * @throws DuplicateEntryException
			 * @throws IntegrityException
			 * @throws StorageException
			 */
			public function testInsertException2() {
				$this->expectException(NullValueException::class);
				$this->entityManager->create(FooSchema::class, [
					'name'  => null,
					'label' => 'kaboom',
				])->save();
			}

			/**
			 * @throws DuplicateEntryException
			 * @throws IntegrityException
			 * @throws StorageException
			 */
			public function testInsertUnique() {
				$this->expectException(DuplicateEntryException::class);
				$this->entityManager->create(FooSchema::class, [
					'name' => 'unique',
				])->save();
				$this->entityManager->create(FooSchema::class, [
					'name' => 'unique',
				])->save();
			}

			/**
			 * @throws DuplicateEntryException
			 * @throws StorageException
			 * @throws IntegrityException
			 */
			public function testSave() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'some name for this entity',
					'optional' => 'this string is optional, but I wanna fill it!',
				])->save();
				self::assertNotEmpty($entity->get('guid'));
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'another name',
					'optional' => null,
				]);
				self::assertNotEmpty($entity->get('guid'));
				$entity->save();
				self::assertFalse($entity->isDirty(), 'Entity is still dirty!');
			}

			public function testCollection() {
				$entityList = [];
				foreach ($this->entityManager->collection(SimpleSchema::class) as $entity) {
					$entity = $entity->toArray();
					unset($entity['guid']);
					$entityList[] = $entity;
				}
				sort($entityList);
				self::assertEquals([
					[
						'name'     => 'another name',
						'optional' => null,
						'value'    => null,
						'date'     => null,
						'question' => null,
					],
					[
						'name'     => 'some name for this entity',
						'optional' => 'this string is optional, but I wanna fill it!',
						'value'    => null,
						'date'     => null,
						'question' => null,
					],
					[
						'name'     => 'this entity is new',
						'optional' => 'foo-bar',
						'value'    => null,
						'date'     => null,
						'question' => null,
					],
				], $entityList);
			}

			/**
			 * @throws DuplicateEntryException
			 * @throws IntegrityException
			 * @throws StorageException
			 */
			public function testUpdate() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'to-be-updated',
					'optional' => null,
					'value'    => 3.14,
					'date'     => new \DateTime('24.12.2020 12:24:13'),
					'question' => false,
				])->save();
				$entity->set('optional', 'this is a new nice and updated string');
				$expect = $entity->toArray();
				$entity->save();
				$collection = $this->entityManager->collection(SimpleSchema::class);
				$query = $collection->getQuery();
				$query->schema(SimpleSchema::class, 'c')->where()->and()->eq('guid')->to($entity->get('guid'));
				$entity = $collection->getEntity();
				self::assertFalse($entity->isDirty(), 'entity should NOT be dirty right after load!');
				self::assertEquals($expect, $array = $entity->toArray());
				self::assertTrue(($type = gettype($array['value'])) === 'double', 'value [' . $type . '] is not float!');
				self::assertInstanceOf(\DateTime::class, $array['date']);
				self::assertTrue(($type = gettype($array['question'])) === 'boolean', 'question [' . $type . '] is not bool!');
				self::assertFalse($array['question']);
			}

			/**
			 * @throws StorageException
			 * @throws UnknownSchemaException
			 */
			public function testRelationTo() {
				$foo = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo The First',
				]);
				self::assertFalse($foo->exists());
				$entity = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo The Second',
				])->save();
				self::assertTrue($entity->exists());
				$bar = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Second',
				]);
				self::assertFalse($bar->exists());
				$bar2 = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Third',
				]);
				self::assertFalse($bar2->exists());
				$this->schemaManager->load(FooBarSchema::class);
				$foo->attach($bar);
				$foo->attach($bar2);
				$foo->save();
				self::assertTrue($foo->exists());
				self::assertTrue($bar->exists());
				self::assertTrue($bar2->exists());
			}

			/**
			 * @throws EntityNotFoundException
			 */
			public function testRelation() {
				$foo = $this->entityManager->collection(FooSchema::class)->entity('foo The First');
				self::assertSame('foo The First', $foo->get('name'));
				$expect = [
					'bar The Second',
					'bar The Third',
				];
				$current = [];
//MATCH
//				(c:`Edde\Test\BarSchema`)-[r:`Edde\Test\FooBarSchema` {foo: '5956cc99-6d01-4d6c-80a2-f0e9a707bf08'}]-(t:`Edde\Test\FooSchema`)
//RETURN
//	c
				foreach ($foo->relationOf(BarSchema::class, FooBarSchema::class) as $bar) {
					$current[] = $bar->get('name');
				}
				sort($expect);
				sort($current);
				self::assertSame($expect, $current, 'entities are not same or not loaded by the collection!');
			}
		}
