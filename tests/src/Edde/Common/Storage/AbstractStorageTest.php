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
		use Edde\Common\Schema\BarSchema;
		use Edde\Common\Schema\FooBarSchema;
		use Edde\Common\Schema\FooSchema;
		use Edde\Common\Schema\SimpleSchema;
		use Edde\Ext\Test\TestCase;

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

			public function testInsert() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'this entity is new',
					'optional' => 'foo-bar',
				])->insert();
				self::assertNotEmpty($entity->get('guid'));
			}

			public function testInsertException() {
				$this->expectException(NullValueException::class);
				$this->entityManager->create(FooSchema::class, [
					'label' => 'kaboom',
				])->insert();
			}

			public function testInsertException2() {
				$this->expectException(NullValueException::class);
				$this->entityManager->create(FooSchema::class, [
					'name'  => null,
					'label' => 'kaboom',
				])->insert();
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
				])->save();
				self::assertFalse($entity->isDirty(), 'Entity is still dirty!');
				self::assertNotEmpty($entity->get('guid'));
			}

			/**
			 * @throws EntityNotFoundException
			 * @throws \Exception
			 */
			public function testUpdate() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'to-be-updated',
					'optional' => null,
					'value'    => 3.14,
					'date'     => new \DateTime('24.12.2020 12:24:13'),
					'question' => false,
				])->insert();
				$entity->set('optional', 'this is a new nice and updated string');
				$expect = $entity->toArray();
				$entity->update();
				$entity = $entity->collection()->load($entity->get('guid'));
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
			public function testRelationTo() {
				$foo = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo The First',
				]);
				$foo2 = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo The Second',
				])->save();
				$bar = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Second',
				]);
				$bar2 = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Third',
				]);
//				$foo->attach()
				$foo->relationTo($bar, FooBarSchema::class)->save();
				$foo->relationTo($bar2, FooBarSchema::class)->save();
				/**
				 * second save of the same entities will survive, because save is checking presence by primary
				 * keys and FooBarSchema is using it's properties as a primary key
				 */
				$foo->relationTo($bar, FooBarSchema::class)->save();
				self::assertTrue(true, 'yay!!');
			}

			/**
			 * @throws EntityNotFoundException
			 * @throws UnknownTableException
			 */
			public function testRelation() {
				$foo = $this->storage->collection(FooSchema::class)->load('foo The First');
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
