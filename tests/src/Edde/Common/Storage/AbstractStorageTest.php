<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

		use Edde\Api\Entity\Inject\EntityManager;
		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\Inject\SchemaManager;
		use Edde\Api\Storage\Exception\DuplicateEntryException;
		use Edde\Api\Storage\Exception\EntityNotFoundException;
		use Edde\Api\Storage\Exception\NullValueException;
		use Edde\Api\Storage\Exception\UnknownTableException;
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
		use Edde\Common\Schema\UserRoleSchema;
		use Edde\Common\Schema\UserSchema;
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
				$schemaList = [
					SimpleSchema::class,
					FooSchema::class,
					PooSchema::class,
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
				$transaction = $this->entityManager->transaction();
				$transaction->queue($entity);
				self::assertNotEmpty($entity->get('guid'));
				self::assertFalse($transaction->isEmpty(), 'there is nothing in the transaction!');
				$transaction->execute();
				self::assertFalse($entity->isDirty(), 'entity is still dirty, oops!');
			}

			public function testInsertException() {
				$this->expectException(NullValueException::class);
				$entity = $this->entityManager->create(FooSchema::class, [
					'label' => 'kaboom',
				]);
				$transaction = $this->entityManager->transaction();
				$transaction->queue($entity);
				$transaction->execute();
			}

			public function testInsertException2() {
				$this->expectException(NullValueException::class);
				$entity = $this->entityManager->create(FooSchema::class, [
					'name'  => null,
					'label' => 'kaboom',
				]);
				$transaction = $this->entityManager->transaction();
				$transaction->queue($entity);
				$transaction->execute();
			}

			public function testInsertUnique() {
				$this->expectException(DuplicateEntryException::class);
				$transaction = $this->entityManager->transaction();
				$transaction->queue($this->entityManager->create(FooSchema::class, [
					'name' => 'unique',
				]));
				$transaction->queue($this->entityManager->create(FooSchema::class, [
					'name' => 'unique',
				]));
				$transaction->execute();
			}

			public function testSave() {
				$transaction = $this->entityManager->transaction();
				$transaction->queue($entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'some name for this entity',
					'optional' => 'this string is optional, but I wanna fill it!',
				]));
				self::assertNotEmpty($entity->get('guid'));
				$transaction->queue($entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'another name',
					'optional' => null,
				]));
				self::assertNotEmpty($entity->get('guid'));
				$transaction->execute();
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
				$transaction = $this->entityManager->transaction();
				$transaction->queue($entity = $this->entityManager->create(SimpleSchema::class, [
					'name'     => 'to-be-updated',
					'optional' => 'this is a new nice and updated string',
					'value'    => 3.14,
					'date'     => new \DateTime('24.12.2020 12:24:13'),
					'question' => false,
				]));
				$transaction->execute();
				$transaction->queue($entity);
				$entity->set('optional', null);
				$expect = $entity->toArray();
				$transaction->execute();
				$entity = $this->entityManager->collection(SimpleSchema::class)->entity($entity->get('guid'));
				self::assertFalse($entity->isDirty(), 'entity should NOT be dirty right after load!');
				self::assertEquals($expect, $array = $entity->toArray());
				self::assertTrue(($type = gettype($array['value'])) === 'double', 'value [' . $type . '] is not float!');
				self::assertInstanceOf(\DateTime::class, $array['date']);
				self::assertTrue(($type = gettype($array['question'])) === 'boolean', 'question [' . $type . '] is not bool!');
				self::assertFalse($array['question']);
			}

			public function testLink() {
				$transaction = $this->entityManager->transaction();
				$transaction->queue($foo = $this->entityManager->create(FooSchema::class, [
					'name'  => 'foo with poo',
					'label' => 'I wanna have a label on this one',
				]));
				$transaction->queue($poo = $this->entityManager->create(PooSchema::class, [
					'name'  => 'the name of this epic Poo!',
					'label' => 'smells like Hell',
				]));
				$transaction->queue($anotherPoo = $this->entityManager->create(PooSchema::class, [
					'name' => 'this is another poo!',
				]));
				$foo->linkTo($poo);
				$foo->linkTo($anotherPoo);
				$foo->linkTo($poo);
				$transaction->execute();
				$source = null;
				foreach ($this->storage->native('MATCH (a:foo)-[:poo]->(p:poo) WHERE a.guid = $a RETURN p', [
					'a' => $foo->get('guid'),
				]) as $source) {
					break;
				}
				self::assertArrayHasKey('name', $source);
				self::assertSame('the name of this epic Poo!', $source['name']);
			}

			/**
			 * @throws EntityNotFoundException
			 * @throws UnknownSchemaException
			 * @throws UnknownTableException
			 */
			public function testSelectQuery() {
				$collection = $this->entityManager->collection(FooSchema::class);
				/**
				 * this query should return the first entity with foo->poo relation (as there is no where
				 * to limit)
				 */
				$collection->query($query = new SelectQuery($this->schemaManager->load(FooSchema::class), 'f'));
				$query->link(PooSchema::class, 'p')->return()->order('p.name');
				$entity = $collection->getEntity();
				self::assertSame('the name of this epic Poo!', $entity->get('name'));
			}

			/**
			 * @throws EntityNotFoundException
			 * @throws UnknownSchemaException
			 * @throws UnknownTableException
			 */
			public function testUnlink() {
				$this->expectException(EntityNotFoundException::class);
				$transaction = $this->entityManager->transaction();
				$transaction->queue($foo = $this->entityManager->collection(FooSchema::class)->entity('foo with poo'));
				/**
				 * there should be just exactly one relation, thus it's not necessary to say which poo should be unlinked
				 */
				$foo->unlink(PooSchema::class);
				$transaction->execute();
				$collection = $this->entityManager->collection(FooSchema::class);
				$collection->query($query = new SelectQuery($this->schemaManager->load(FooSchema::class), 'f'));
				$query->link(PooSchema::class, 'p')->return()->order('p.name');
				$collection->getEntity();
			}

			/**
			 * @throws UnknownSchemaException
			 */
			public function testRelationTo() {
				$transaction = $this->entityManager->transaction();
				$transaction->queue($foo = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo The First',
				]));
				$transaction->queue($foo2 = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo The Second',
				]));
				$transaction->queue($bar = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Second',
				]));
				$transaction->queue($bar2 = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar The Third',
				]));
				$transaction->queue($bar3 = $this->entityManager->create(BarSchema::class, [
					'name' => 'Bar for The Foo',
				]));
				$transaction->queue($this->entityManager->create(BarSchema::class, [
					'name' => 'Another, very secret bar!',
				]));
				$transaction->queue($poo = $this->entityManager->create(PooSchema::class, [
					'name' => 'Da Poo The First One!',
				]));
				$transaction->queue($poo2 = $this->entityManager->create(PooSchema::class, [
					'name' => 'Da Poo The Bigger One!',
				]));
				$transaction->queue($poo3 = $this->entityManager->create(PooSchema::class, [
					'name' => 'Da Poo The Hard One!',
				]));
				$this->schemaManager->load(FooBarSchema::class);
				$this->schemaManager->load(BarPooSchema::class);
				$foo->attach($bar);
				$foo->attach($bar2);
				$bar2->attach($poo);
				$bar2->attach($poo2);
				$bar2->attach($poo3);
				$foo2->attach($bar3);
				$transaction->execute();
				$barList = [];
				$expected = [
					'bar The Second',
					'bar The Third',
				];
				foreach ($foo->join(BarSchema::class, 'b') as $entity) {
					self::assertSame(BarSchema::class, $entity->getSchema()->getName());
					$barList[] = $entity->get('name');
				}
				sort($barList);
				sort($expected);
				self::assertEquals($expected, $barList);
				$barList = [];
				$expected = [
					'Bar for The Foo',
				];
				foreach ($foo2->join(BarSchema::class, 'b') as $entity) {
					self::assertSame(BarSchema::class, $entity->getSchema()->getName());
					$barList[] = $entity->get('name');
				}
				sort($barList);
				sort($expected);
				self::assertEquals($expected, $barList);
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
					self::assertSame(PooSchema::class, $poo->getSchema()->getName());
					$current[] = $poo->get('name');
				}
				sort($expect);
				sort($current);
				self::assertSame($expect, $current, 'entities are not same or not loaded by the collection!');
			}

			/**
			 * @throws UnknownSchemaException
			 */
			public function testRelationAttribute() {
				$this->schemaManager->load(UserRoleSchema::class);
				$transaction = $this->entityManager->transaction();
				$transaction->queue($user = $this->entityManager->create(UserSchema::class, [
					'name'    => 'Me, The Best User Ever!',
					'email'   => 'me@there.here',
					'created' => new \DateTime(),
				]));
				$transaction->queue($root = $this->entityManager->create(RoleSchema::class, [
					'name'  => 'root',
					'label' => 'he can do everything!',
				]));
				$transaction->queue($guest = $this->entityManager->create(RoleSchema::class, [
					'name'  => 'guest',
					'label' => 'this one can do almost nothing!',
				]));
				$user->attach($root)->set('enabled', false);
				$user->attach($root)->set('enabled', true);
				$user->attach($guest)->set('enabled', false);
				$transaction->execute();
				$expect = [
					'root',
				];
				$query = new SelectQuery(($userSchema = $user->getSchema()), 'u');
				$query->join(RoleSchema::class, 'r')->return('r');
				$query->where('u.name', '=', 'Me, The Best User Ever!');
				$query->where('u\r.enabled', '=', 1);
				$current = [];
				foreach ($this->storage->execute($query) as $source) {
					$current[] = $source['name'];
				}
				sort($expect);
				sort($current);
				self::assertSame($expect, $current, 'looks like roles are not properly assigned!');
				$current = [];
				foreach ($user->join(RoleSchema::class, 'r')->where('c\r.enabled', '=', 1) as $role) {
					self::assertEquals(RoleSchema::class, $role->getSchema()->getName());
					$current[] = $role->get('name');
				}
				sort($expect);
				sort($current);
				self::assertSame($expect, $current, 'looks like roles are not properly assigned!');
				$current = [];
				foreach ($user->join(RoleSchema::class, 'r') as $role) {
					$current[] = $role->get('name');
				}
				self::assertSame(['root'], $current);
			}

			/**
			 * @throws UnknownSchemaException
			 */
			public function testBenchmark() {
				$this->schemaManager->load(FooBarSchema::class);
				$this->schemaManager->load(BarPooSchema::class);
				$this->storage->start();
				$start = microtime(true);
				for ($i = 0; $i < 10; $i++) {
					$transaction = $this->entityManager->transaction();
					$transaction->queue($foo = $this->entityManager->create(FooSchema::class, [
						'name' => 'foo #' . $i,
					]));
					$transaction->queue($poo = $this->entityManager->create(PooSchema::class, [
						'name'  => 'poo of foo $' . $i,
						'label' => "and it's labeled #$i",
					]));
					$transaction->queue($bar = $this->entityManager->create(BarSchema::class, [
						'name' => 'bar #' . $i,
					]));
					$transaction->queue($bar2 = $this->entityManager->create(BarSchema::class, [
						'name' => 'bar 2 #' . $i,
					]));
					$foo->linkTo($poo);
//					$foo->attach($bar);
//					$foo->attach($bar2);
					$bar->linkTo($poo);
					$transaction->execute();
				}
				$this->storage->commit();
				$sum = (microtime(true) - $start);
				$item = ($sum / $i) * 1000;
				fwrite(STDERR, sprintf("[%s] %.4fs, %.4f ms/operation (%.2f%% of current limit)\n", static::class, $sum, $item, (100 * $item) / ($limit = $this->getEntityTimeLimit())));
				self::assertLessThanOrEqual($limit, $item);
			}

			abstract protected function getEntityTimeLimit(): float;
		}
