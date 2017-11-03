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
		use Edde\Common\Schema\BarPooSchema;
		use Edde\Common\Schema\BarSchema;
		use Edde\Common\Schema\FooBarSchema;
		use Edde\Common\Schema\FooSchema;
		use Edde\Common\Schema\PooSchema;
		use Edde\Common\Schema\SimpleSchema;
		use Edde\Common\Schema\SubBarSchema;
		use Edde\Ext\Test\TestCase;

		abstract class AbstractStorageTest extends TestCase {
			use EntityManager;
			use SchemaManager;
			use Storage;

			/**
			 * @throws UnknownSchemaException
			 */
			public function testCreateSchema() {
				$this->storage->start();
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(SimpleSchema::class)));
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(FooSchema::class)));
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(PooSchema::class)));
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(SubBarSchema::class)));
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(BarSchema::class)));
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(FooBarSchema::class)));
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load(BarPooSchema::class)));
				$this->storage->commit();
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
					'optional' => 'this is a new nice and updated string',
					'value'    => 3.14,
					'date'     => new \DateTime('24.12.2020 12:24:13'),
					'question' => false,
				])->save();
				$entity->set('optional', null);
				$expect = $entity->toArray();
				$entity->save();
				$entity = $this->entityManager->collection(SimpleSchema::class)->entity($entity->get('guid'));
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
				$foo2 = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo The Second',
				]);
				$bar = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Second',
				]);
				self::assertFalse($bar->exists());
				$bar2 = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Third',
				]);
				$bar3 = $this->entityManager->create(BarSchema::class, [
					'name' => 'Bar for The Foo',
				]);
				$this->entityManager->create(BarSchema::class, [
					'name' => 'Another, very secret bar!',
				])->save();
				$poo = $this->entityManager->create(PooSchema::class, [
					'name' => 'Da Poo The First One!',
				]);
				$poo2 = $this->entityManager->create(PooSchema::class, [
					'name' => 'Da Poo The Bigger One!',
				]);
				$poo3 = $this->entityManager->create(PooSchema::class, [
					'name' => 'Da Poo The Hard One!',
				]);
				self::assertFalse($bar2->exists());
				$this->schemaManager->load(FooBarSchema::class);
				$this->schemaManager->load(BarPooSchema::class);
				$foo->attach($bar);
				$foo->attach($bar2);
				$bar2->attach($poo);
				$bar2->attach($poo2);
				$bar2->attach($poo3);
				$foo->save();
				$foo2->attach($bar3);
				$foo2->save();
				self::assertTrue($foo->exists());
				self::assertTrue($foo2->exists());
				self::assertTrue($bar->exists());
				self::assertTrue($bar2->exists());
				self::assertTrue($bar3->exists());
				self::assertTrue($poo->exists());
				self::assertTrue($poo2->exists());
				self::assertTrue($poo3->exists());
			}

			/**
			 * @throws EntityNotFoundException
			 * @throws UnknownSchemaException
			 */
			public function testRelation() {
				$this->schemaManager->load(FooBarSchema::class);
				$foo = $this->entityManager->collection(FooSchema::class)->entity('foo The First');
				self::assertSame('foo The First', $foo->get('name'));
				$expect = [
					'bar The Second',
					'bar The Third',
					'Bar for The Foo',
				];
				$current = [];
				foreach ($this->entityManager->collection(FooSchema::class)->join(BarSchema::class, 'b') as $bar) {
					$current[] = $bar->get('name');
				}
				sort($expect);
				sort($current);
				self::assertSame($expect, $current, 'entities are not same or not loaded by the collection!');
				$expect = [
					'bar The Second',
					'bar The Third',
				];
				$current = [];
				foreach ($foo->join(BarSchema::class, 'b') as $bar) {
					$current[] = $bar->get('name');
				}
				sort($expect);
				sort($current);
				self::assertSame($expect, $current, 'entities are not same or not loaded by the collection!');
			}

			/**
			 * @throws EntityNotFoundException
			 * @throws UnknownSchemaException
			 */
			public function testRelationOfRelation() {
				$this->schemaManager->load(FooBarSchema::class);
				$this->schemaManager->load(BarPooSchema::class);
				$foo = $this->entityManager->collection(FooSchema::class)->entity('foo The First');
				self::assertSame('foo The First', $foo->get('name'));
				$expect = [
					'Da Poo The First One!',
					'Da Poo The Bigger One!',
					'Da Poo The Hard One!',
				];
				$current = [];
				foreach ($foo->join(BarSchema::class, 'b')->join(PooSchema::class, 'p') as $poo) {
					$current[] = $poo->get('name');
				}
				sort($expect);
				sort($current);
				self::assertSame($expect, $current, 'entities are not same or not loaded by the collection!');
			}
		}
