<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Entity\Inject\Transaction;
		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\IntegrityException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\StorageException;
		use Edde\Api\Storage\Inject\Storage;
		use Edde\Common\Query\CreateSchemaQuery;
		use Edde\Common\Query\SelectQuery;
		use Edde\Common\Schema\BarPooSchema;
		use Edde\Common\Schema\BarSchema;
		use Edde\Common\Schema\FooBarSchema;
		use Edde\Common\Schema\FooSchema;
		use Edde\Common\Schema\PooSchema;
		use Edde\Common\Schema\RoleSchema;
		use Edde\Common\Schema\SimpleSchema;
		use Edde\Common\Schema\SubBarSchema;
		use Edde\Common\Schema\UserRoleSchema;
		use Edde\Common\Schema\UserSchema;
		use Edde\Ext\Test\TestCase;

		abstract class AbstractStorageTest extends TestCase {
			use EntityManager;
			use SchemaManager;
			use Transaction;
			use Storage;

			/**
			 * @throws UnknownSchemaException
			 */
			public function testCreateSchema() {
				$this->storage->start();
				$schemaList = [
					SimpleSchema::class,
					FooSchema::class,
					PooSchema::class,
					SubBarSchema::class,
					BarSchema::class,
					FooBarSchema::class,
					BarPooSchema::class,
					UserSchema::class,
					RoleSchema::class,
					UserRoleSchema::class,
				];
				foreach ($schemaList as $name) {
					$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load($name)));
				}
				$this->storage->commit();
				self::assertTrue(true, 'everything is ok');
			}

			public function testInsert() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'this entity is new',
					'optional' => 'foo-bar',
				]);
				self::assertNotEmpty($entity->get('guid'));
				self::assertFalse($this->transaction->isEmpty(), 'there is nothing in the transaction!');
				$this->transaction->execute();
				self::assertFalse($entity->isDirty(), 'entity is still dirty, oops!');
			}

			public function testInsertException() {
				$this->expectException(NullValueException::class);
				$this->entityManager->create(FooSchema::class, [
					'label' => 'kaboom',
				]);
				$this->transaction->execute();
			}

			public function testInsertException2() {
				$this->expectException(NullValueException::class);
				$this->entityManager->create(FooSchema::class, [
					'name'  => null,
					'label' => 'kaboom',
				]);
				$this->transaction->execute();
			}

			public function testInsertUnique() {
				$this->expectException(DuplicateEntryException::class);
				$this->entityManager->create(FooSchema::class, [
					'name' => 'unique',
				]);
				$this->entityManager->create(FooSchema::class, [
					'name' => 'unique',
				]);
				$this->transaction->execute();
			}

			public function testSave() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'some name for this entity',
					'optional' => 'this string is optional, but I wanna fill it!',
				]);
				self::assertNotEmpty($entity->get('guid'));
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'another name',
					'optional' => null,
				]);
				self::assertNotEmpty($entity->get('guid'));
				$this->transaction->execute();
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
			 * @throws EntityNotFoundException
			 */
			public function testUpdate() {
				$entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'to-be-updated',
					'optional' => 'this is a new nice and updated string',
					'value'    => 3.14,
					'date'     => new \DateTime('24.12.2020 12:24:13'),
					'question' => false,
				]);
				$this->transaction->execute();
				$this->transaction->entity($entity);
				$entity->set('optional', null);
				$expect = $entity->toArray();
				$this->transaction->execute();
				$entity = $this->entityManager->collection(SimpleSchema::class)->entity($entity->get('guid'));
				self::assertFalse($entity->isDirty(), 'entity should NOT be dirty right after load!');
				self::assertEquals($expect, $array = $entity->toArray());
				self::assertTrue(($type = gettype($array['value'])) === 'double', 'value [' . $type . '] is not float!');
				self::assertInstanceOf(\DateTime::class, $array['date']);
				self::assertTrue(($type = gettype($array['question'])) === 'boolean', 'question [' . $type . '] is not bool!');
				self::assertFalse($array['question']);
			}

			/**
			 * @throws UnknownSchemaException
			 */
			public function testRelationTo() {
				$foo = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo The First',
				]);
				$foo2 = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo The Second',
				]);
				$bar = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Second',
				]);
				$bar2 = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Third',
				]);
				$bar3 = $this->entityManager->create(BarSchema::class, [
					'name' => 'Bar for The Foo',
				]);
				$this->entityManager->create(BarSchema::class, [
					'name' => 'Another, very secret bar!',
				]);
				$poo = $this->entityManager->create(PooSchema::class, [
					'name' => 'Da Poo The First One!',
				]);
				$poo2 = $this->entityManager->create(PooSchema::class, [
					'name' => 'Da Poo The Bigger One!',
				]);
				$poo3 = $this->entityManager->create(PooSchema::class, [
					'name' => 'Da Poo The Hard One!',
				]);
				$this->schemaManager->load(FooBarSchema::class);
				$this->schemaManager->load(BarPooSchema::class);
//				$foo->attach($bar);
//				$foo->attach($bar2);
//				$bar2->attach($poo);
//				$bar2->attach($poo2);
//				$bar2->attach($poo3);
//				$foo2->attach($bar3);
				$this->transaction->execute();
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
					self::assertEquals(BarSchema::class, $bar->getSchema()->getName());
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

			/**
			 * @throws DuplicateEntryException
			 * @throws IntegrityException
			 * @throws StorageException
			 * @throws UnknownSchemaException
			 */
			public function testRelationAttribute() {
				$this->schemaManager->load(UserRoleSchema::class);
				$user = $this->entityManager->create(UserSchema::class, [
					'name'    => 'Me, The Best User Ever!',
					'email'   => 'me@there.here',
					'created' => new \DateTime(),
				]);
				$root = $this->entityManager->create(RoleSchema::class, [
					'name'  => 'root',
					'label' => 'he can do everything!',
				]);
				$guest = $this->entityManager->create(RoleSchema::class, [
					'name'  => 'guest',
					'label' => 'this one can do almost nothing!',
				]);
				$root->attach($user)->set('enabled', false);
				$root->attach($user)->set('enabled', true);
				$guest->attach($user)->set('enabled', false);
				$root->save();
				$guest->save();
				$expect = [
					'root',
				];
				$query = new SelectQuery(($userSchema = $user->getSchema()), 'u');
				$query->join(RoleSchema::class, 'r')->select('r');
				$query->where('u.name', '=', 'Me, The Best User Ever!');
				$query->where('u\r.enabled', '=', true);
				$current = [];
				foreach ($this->storage->execute($query) as $source) {
					$current[] = $source['name'];
				}
				sort($expect);
				sort($current);
				self::assertSame($expect, $current, 'looks like roles are not properly assigned!');
				$current = [];
				foreach ($user->join(RoleSchema::class, 'r')->where('c\r.enabled', '=', true) as $role) {
					self::assertEquals(RoleSchema::class, $role->getSchema()->getName());
					$current[] = $role->get('name');
				}
				sort($expect);
				sort($current);
				self::assertSame($expect, $current, 'looks like roles are not properly assigned!');
			}

			/**
			 * @throws DuplicateEntryException
			 * @throws IntegrityException
			 * @throws StorageException
			 */
			public function testSimpleRelation() {
				$bar = $this->entityManager->create(BarSchema::class, [
					'name' => 'simple-relation',
				]);
				$subBar = $this->entityManager->create(SubBarSchema::class, [
					'label' => 'some-label',
				]);
				$subBaBar = $this->entityManager->create(SubBarSchema::class, [
					'label' => 'another-label',
				]);
				$bar->link($subBar);
				$bar->save();
				self::assertTrue($bar->exists());
				self::assertTrue($subBar->exists());
				$bar = $this->entityManager->collection(BarSchema::class)->entity('simple-relation');
				self::assertSame('simple-relation', $bar->get('name'));
				$subBar = $bar->entity('subBar');
				self::assertSame('some-label', $subBar->get('label'));
				$bar->link($subBaBar);
				$bar->save();
				$subBar = $bar->entity('subBar');
				self::assertSame('another-label', $subBar->get('label'));
			}

			/**
			 * @throws DuplicateEntryException
			 * @throws IntegrityException
			 * @throws StorageException
			 * @throws UnknownSchemaException
			 */
			public function testBenchmark() {
				$this->schemaManager->load(FooBarSchema::class);
				$this->schemaManager->load(BarPooSchema::class);
				$this->storage->start();
				$start = microtime(true);
				for ($i = 0; $i < 100; $i++) {
					$foo = $this->entityManager->create(FooSchema::class, [
						'name' => 'foo #' . $i,
					]);
					$bar = $this->entityManager->create(BarSchema::class, [
						'name' => 'bar #' . $i,
					]);
					$bar2 = $this->entityManager->create(BarSchema::class, [
						'name' => 'bar 2 #' . $i,
					]);
					$subBar = $this->entityManager->create(SubBarSchema::class, [
						'label' => 'sub-bar #' . $i,
					]);
					$subBar2 = $this->entityManager->create(SubBarSchema::class, [
						'label' => 'sub-bar 2 #' . $i,
					]);
					$bar->link($subBar);
					$foo->attach($bar);
					$foo->attach($bar2);
					$foo->save();
					$bar->link($subBar2);
					$bar->save();
				}
				$this->storage->commit();
				$sum = (microtime(true) - $start);
				$item = ($sum / $i) * 1000;
				fwrite(STDERR, sprintf("[%s] %.4fs, %.4f ms/operation (%.2f%% of current limit)\n", static::class, $sum, $item, (100 * $item) / ($limit = $this->getEntityTimeLimit())));
				self::assertLessThanOrEqual($limit, $item);
			}

			abstract protected function getEntityTimeLimit(): float;
		}
