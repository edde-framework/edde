<?php
	declare(strict_types=1);
	namespace Edde\Common\Storage;

	use DateTime;
	use Edde\Api\Driver\Exception\DriverException;
	use Edde\Api\Entity\Exception\RecordException;
	use Edde\Api\Entity\Exception\UnknownAliasException;
	use Edde\Api\Entity\IEntity;
	use Edde\Api\Entity\Inject\EntityManager;
	use Edde\Api\Schema\Exception\InvalidRelationException;
	use Edde\Api\Schema\Exception\NoPrimaryPropertyException;
	use Edde\Api\Schema\Exception\RelationException;
	use Edde\Api\Schema\Exception\SchemaException;
	use Edde\Api\Schema\Exception\UnknownPropertyException;
	use Edde\Api\Schema\Exception\UnknownSchemaException;
	use Edde\Api\Schema\Inject\SchemaManager;
	use Edde\Api\Storage\Exception\DuplicateEntryException;
	use Edde\Api\Storage\Exception\DuplicateTableException;
	use Edde\Api\Storage\Exception\EntityNotFoundException;
	use Edde\Api\Storage\Exception\ExclusiveTransactionException;
	use Edde\Api\Storage\Exception\NoTransactionException;
	use Edde\Api\Storage\Exception\StorageException;
	use Edde\Api\Storage\Exception\UnknownTableException;
	use Edde\Api\Storage\Inject\Storage;
	use Edde\Api\Validator\Exception\BatchValidationException;
	use Edde\Api\Validator\Exception\ValidationException;
	use Edde\Common\Schema\BarPooSchema;
	use Edde\Common\Schema\BarSchema;
	use Edde\Common\Schema\FooBarSchema;
	use Edde\Common\Schema\FooSchema;
	use Edde\Common\Schema\PooSchema;
	use Edde\Common\Schema\RoleSchema;
	use Edde\Common\Schema\SimpleSchema;
	use Edde\Common\Schema\SourceOneTargetSchema;
	use Edde\Common\Schema\SourceSchema;
	use Edde\Common\Schema\SourceTwoTargetSchema;
	use Edde\Common\Schema\TargetSchema;
	use Edde\Common\Schema\UserRoleSchema;
	use Edde\Common\Schema\UserSchema;
	use Edde\Common\Storage\Query\CreateSchemaQuery;
	use Edde\Common\Storage\Query\SelectQuery;
	use Edde\Ext\Test\TestCase;

	abstract class AbstractStorageTest extends TestCase {
		use EntityManager;
		use SchemaManager;
		use Storage;

		/**
		 * @throws DriverException
		 * @throws DuplicateTableException
		 * @throws StorageException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws ExclusiveTransactionException
		 * @throws NoTransactionException
		 */
		public function testCreateSchema() {
			$this->storage->start();
			$schemas = [
				SimpleSchema::class,
				PooSchema::class,
				FooSchema::class,
				BarSchema::class,
				FooBarSchema::class,
				BarPooSchema::class,
				UserSchema::class,
				RoleSchema::class,
				UserRoleSchema::class,
				SourceSchema::class,
				TargetSchema::class,
				SourceOneTargetSchema::class,
				SourceTwoTargetSchema::class,
			];
			foreach ($schemas as $name) {
				$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load($name)));
			}
			$this->storage->commit();
			self::assertTrue(true, 'everything is ok');
		}

		/**
		 * @throws SchemaException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
		 */
		public function testValidator() {
			$this->expectException(BatchValidationException::class);
			$this->expectExceptionMessage('Validation of schema [Edde\Common\Schema\SimpleSchema] failed.');
			$entity = $this->entityManager->create(SimpleSchema::class, ['name' => true]);
			$entity->validate();
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws SchemaException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
		 */
		public function testInsert() {
			$entity = $this->entityManager->create(SimpleSchema::class, [
				'name'     => 'this entity is new',
				'optional' => 'foo-bar',
			]);
			self::assertNotEmpty($entity->get('uuid'));
			$entity->save();
			self::assertFalse($entity->isDirty(), 'entity is still dirty, oops!');
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws SchemaException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
		 */
		public function testInsertException() {
			$this->expectException(BatchValidationException::class);
			$this->expectExceptionMessage('Validation of schema [Edde\Common\Schema\FooSchema] failed.');
			$this->entityManager->create(FooSchema::class, [
				'label' => 'kaboom',
			])->save();
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws SchemaException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
		 */
		public function testInsertException2() {
			$this->expectException(BatchValidationException::class);
			$this->expectExceptionMessage('Validation of schema [Edde\Common\Schema\FooSchema] failed.');
			$this->entityManager->create(FooSchema::class, [
				'name'  => null,
				'label' => 'kaboom',
			])->save();
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws SchemaException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
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
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws SchemaException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
		 */
		public function testSave() {
			$entity = $this->entityManager->create(SimpleSchema::class, [
				'name'     => 'some name for this entity',
				'optional' => 'this string is optional, but I wanna fill it!',
			])->save();
			self::assertNotEmpty($entity->get('uuid'));
			$entity = $this->entityManager->create(SimpleSchema::class, [
				'name'     => 'another name',
				'optional' => null,
			])->save();
			self::assertNotEmpty($entity->get('uuid'));
			self::assertFalse($entity->isDirty(), 'Entity is still dirty!');
		}

		/**
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownSchemaException
		 */
		public function testCollection() {
			$entities = [];
			foreach ($this->entityManager->collection('c', SimpleSchema::class) as $record) {
				$entity = $record->getEntity('c')->toArray();
				unset($entity['uuid']);
				$entities[] = $entity;
			}
			sort($entities);
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
			], $entities);
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws EntityNotFoundException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownAliasException
		 * @throws UnknownSchemaException
		 * @throws UnknownTableException
		 * @throws ValidationException
		 */
		public function testUpdate() {
			$entity = $this->entityManager->create(SimpleSchema::class, [
				'name'     => 'to-be-updated',
				'optional' => 'this is a new nice and updated string',
				'value'    => 3.14,
				'date'     => new DateTime('24.12.2020 12:24:13'),
				'question' => false,
			])->save();
			$entity->set('optional', null);
			$expect = $entity->toArray();
			$entity->save();
			$entity = $this->entityManager->collection('c', SimpleSchema::class)->entity('c', $entity->get('uuid'));
			self::assertFalse($entity->isDirty(), 'entity should NOT be dirty right after load!');
			self::assertEquals($expect, $array = $entity->toArray());
			self::assertTrue(($type = gettype($array['value'])) === 'double', 'value [' . $type . '] is not float!');
			self::assertInstanceOf(DateTime::class, $array['date']);
			self::assertTrue(($type = gettype($array['question'])) === 'boolean', 'question [' . $type . '] is not bool!');
			self::assertFalse($array['question']);
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws EntityNotFoundException
		 * @throws UnknownSchemaException
		 * @throws UnknownTableException
		 * @throws ValidationException
		 * @throws SchemaException
		 * @throws RecordException
		 */
		public function testLink() {
			$foo = $this->entityManager->create(FooSchema::class, [
				'name'  => 'foo with poo',
				'label' => 'I wanna have a label on this one',
			]);
			$poo = $this->entityManager->create(PooSchema::class, [
				'name'  => 'the name of this epic Poo!',
				'label' => 'smells like Hell',
			]);
			$anotherPoo = $this->entityManager->create(PooSchema::class, [
				'name' => 'this is another poo!',
			]);
			$foo->linkTo($poo);
			$foo->linkTo($anotherPoo);
			$foo->linkTo($poo);
			$foo->save();
			$source = null;
			$collection = $this->entityManager->collection('p', PooSchema::class);
			$collection->query($query = new SelectQuery($this->schemaManager->load(FooSchema::class), 'f'));
			$collection->link('p', PooSchema::class);
			$poo = $collection->getEntity('p');
			self::assertSame(PooSchema::class, $poo->getSchema()->getName());
			self::assertSame('the name of this epic Poo!', $poo->get('name'));
			$poo = $foo->link(PooSchema::class);
			self::assertSame('the name of this epic Poo!', $poo->get('name'));
		}

		/**
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownSchemaException
		 */
		public function testSelectQuery() {
			$collection = $this->entityManager->collection('f', FooSchema::class);
			$collection->link('p', PooSchema::class);
			$collection->order('p.name');
			$record = $collection->getRecord();
			self::assertSame('the name of this epic Poo!', $record->getEntity('p')->get('name'));
			self::assertSame('foo with poo', $record->getEntity('f')->get('name'));
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws EntityNotFoundException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownSchemaException
		 * @throws UnknownTableException
		 * @throws ValidationException
		 * @throws UnknownAliasException
		 * @throws UnknownPropertyException
		 */
		public function testUnlink() {
			$this->expectException(EntityNotFoundException::class);
			$foo = $this->entityManager->collection('f', FooSchema::class)->entity('f', 'foo with poo');
			/**
			 * there should be just exactly one relation, thus it's not necessary to say which poo should be unlinked
			 */
			$foo->unlink(PooSchema::class);
			$foo->save();
			$collection = $this->entityManager->collection('f', FooSchema::class);
			$collection->query($query = new SelectQuery($this->schemaManager->load(FooSchema::class), 'f'));
			$collection->link('p', PooSchema::class);
			$collection->order('p.name');
			$collection->getEntity('p');
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws InvalidRelationException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownAliasException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
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
			$foo->attach($bar);
			$foo->attach($bar2);
			$foo->linkTo($poo);
			$bar2->attach($poo);
			$bar2->attach($poo2);
			$bar2->attach($poo3);
			$foo2->attach($bar3);
			$foo->save();
			$foo2->save();
			$barList = [];
			$expected = [
				'bar The Second',
				'bar The Third',
			];
			/** @var $foo IEntity */
			foreach ($foo->join('b', BarSchema::class) as $record) {
				$foo = $record->getEntity('e');
				$bar = $record->getEntity('b');
				$fooBar = $record->getEntity('e\r');
				self::assertSame(BarSchema::class, $bar->getSchema()->getName());
				self::assertSame(FooSchema::class, $foo->getSchema()->getName());
				self::assertSame(FooBarSchema::class, $fooBar->getSchema()->getName());
				self::assertSame($foo->get('uuid'), $fooBar->get('foo'));
				self::assertSame($bar->get('uuid'), $fooBar->get('bar'));
				$barList[] = $bar->get('name');
			}
			sort($barList);
			sort($expected);
			self::assertEquals($expected, $barList);
			$barList = [];
			$expected = [
				'Bar for The Foo',
			];
			foreach ($foo2->join('b', BarSchema::class) as $record) {
				$entity = $record->getEntity('b');
				self::assertSame(BarSchema::class, $entity->getSchema()->getName());
				$barList[] = $entity->get('name');
			}
			sort($barList);
			sort($expected);
			self::assertEquals($expected, $barList);
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownAliasException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws UnknownTableException
		 * @throws InvalidRelationException
		 */
		public function testRelation() {
			$this->schemaManager->load(FooBarSchema::class);
			$foo = $this->entityManager->collection('f', FooSchema::class)->entity('f', 'foo The First');
			self::assertSame('foo The First', $foo->get('name'));
			$expect = [
				'bar The Second',
				'bar The Third',
				'Bar for The Foo',
			];
			$current = [];
			foreach ($this->entityManager->collection('f', FooSchema::class)->join('f', BarSchema::class, 'b') as $record) {
				$bar = $record->getEntity('b');
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
			foreach ($foo->join('b', BarSchema::class) as $record) {
				$current[] = $record->getEntity('b')->get('name');
			}
			sort($expect);
			sort($current);
			self::assertSame($expect, $current, 'entities are not same or not loaded by the collection!');
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws InvalidRelationException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownAliasException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws UnknownTableException
		 */
		public function testRelationOfRelation() {
			$this->schemaManager->load(FooBarSchema::class);
			$this->schemaManager->load(BarPooSchema::class);
			$foo = $this->entityManager->collection('f', FooSchema::class)->entity('f', 'foo The First');
			self::assertSame('foo The First', $foo->get('name'));
			$expect = [
				'Da Poo The First One!',
				'Da Poo The Bigger One!',
				'Da Poo The Hard One!',
			];
			$current = [];
			foreach ($foo->join('b', BarSchema::class)->join('b', PooSchema::class, 'p') as $record) {
				/**
				 * just test, if those does not throw an exception
				 */
				self::assertSame(FooSchema::class, $record->getEntity('e')->getSchema()->getName());
				self::assertSame(FooBarSchema::class, $record->getEntity('e\r')->getSchema()->getName());
				self::assertSame(BarPooSchema::class, $record->getEntity('b\r')->getSchema()->getName());
				self::assertSame(BarSchema::class, $record->getEntity('b')->getSchema()->getName());
				self::assertSame(PooSchema::class, $record->getEntity('p')->getSchema()->getName());
				$poo = $record->getEntity('p');
				self::assertSame(PooSchema::class, $poo->getSchema()->getName());
				$current[] = $poo->get('name');
			}
			sort($expect);
			sort($current);
			self::assertSame($expect, $current, 'entities are not same or not loaded by the collection!');
		}

		/**
		 * @throws BatchValidationException
		 * @throws DriverException
		 * @throws DuplicateEntryException
		 * @throws DuplicateTableException
		 * @throws InvalidRelationException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws UnknownAliasException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
		 */
		public function testRelationAttribute() {
			$this->schemaManager->load(UserRoleSchema::class);
			$user = $this->entityManager->create(UserSchema::class, [
				'name'    => 'Me, The Best User Ever!',
				'email'   => 'me@there.here',
				'created' => new DateTime(),
			]);
			$root = $this->entityManager->create(RoleSchema::class, [
				'name'  => 'root',
				'label' => 'he can do everything!',
			]);
			$guest = $this->entityManager->create(RoleSchema::class, [
				'name'  => 'guest',
				'label' => 'this one can do almost nothing!',
			]);
			$user->attach($root)->set('enabled', false);
			$user->attach($root)->set('enabled', true);
			$user->attach($guest)->set('enabled', false);
			$user->save();
			$expect = [
				'root',
			];
			self::assertEquals(2, $this->entityManager->collection('r', RoleSchema::class)->count('r'));
			$query = new SelectQuery(($userSchema = $user->getSchema()), 'u');
			$query->join('r', RoleSchema::class);
			$query->alias('r', $this->schemaManager->load(RoleSchema::class));
			$query->alias('u\r', $this->schemaManager->load(UserRoleSchema::class));
			$query->where('u.name', '=', 'Me, The Best User Ever!');
			$query->where('u\r.enabled', '=', 1);
			$current = [];
			foreach ($this->storage->execute($query) as $source) {
				$current[] = $source['r.name'];
			}
			sort($expect);
			sort($current);
			self::assertSame($expect, $current, 'looks like roles are not properly assigned!');
			$current = [];
			foreach ($user->join('r', RoleSchema::class)->where('e\r.enabled', '=', 1) as $record) {
				$user = $record->getEntity('e');
				$userRole = $record->getEntity('e\r');
				$role = $record->getEntity('r');
				self::assertEquals(UserSchema::class, $user->getSchema()->getName());
				self::assertEquals(UserRoleSchema::class, $userRole->getSchema()->getName());
				self::assertEquals(RoleSchema::class, $role->getSchema()->getName());
				$current[] = $role->get('name');
			}
			sort($expect);
			sort($current);
			self::assertSame($expect, $current, 'looks like roles are not properly assigned!');
			$current = [];
			foreach ($user->join('r', RoleSchema::class) as $record) {
				$current[] = $record->getEntity('r')->get('name');
			}
			$expect = [
				'root',
				'root',
				'guest',
			];
			sort($expect);
			sort($current);
			self::assertSame($expect, $current);
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws InvalidRelationException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownAliasException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
		 * @throws RelationException
		 */
		public function testMoreRelations() {
			$this->schemaManager->load(SourceOneTargetSchema::class);
			$this->schemaManager->load(SourceTwoTargetSchema::class);
			$source = $this->entityManager->create(SourceSchema::class);
			$target = $this->entityManager->create(TargetSchema::class);
			$source->set('name', 'source-yapee');
			$target->set('name', 'yapee');
			$source->attach($target, SourceTwoTargetSchema::class);
			$source->save();
			$current = [];
			foreach ($source->join('t', TargetSchema::class, SourceTwoTargetSchema::class) as $record) {
				$current[] = $record->getEntity('t')->get('name');
			}
			self::assertEquals(['yapee'], $current);
			self::assertSame(0, $source->join('t', TargetSchema::class, SourceOneTargetSchema::class)->count('t'));
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws EntityNotFoundException
		 * @throws InvalidRelationException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownAliasException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws UnknownTableException
		 * @throws ValidationException
		 * @throws NoPrimaryPropertyException
		 */
		public function testReverseRelation() {
			$this->schemaManager->load(SourceOneTargetSchema::class);
			$this->schemaManager->load(SourceTwoTargetSchema::class);
			$target = $this->entityManager->create(TargetSchema::class, ['name' => 'boo'])->save();
			$source = $this->entityManager->collection('s', SourceSchema::class)->entity('s', 'source-yapee');
			$source->attach($target, SourceTwoTargetSchema::class);
			$source->save();
			$current = [];
			foreach ($source->join('t', TargetSchema::class, SourceTwoTargetSchema::class) as $record) {
				$current[] = $record->getEntity('t')->get('name');
			}
			sort($current);
			self::assertEquals(['boo', 'yapee'], $current);
			$current = [];
			foreach ($target->reverseJoin('s', SourceSchema::class, SourceTwoTargetSchema::class) as $record) {
				$current[] = $record->getEntity('s')->get('name');
			}
			self::assertEquals(['source-yapee'], $current);
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws EntityNotFoundException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownAliasException
		 * @throws UnknownSchemaException
		 * @throws UnknownTableException
		 * @throws ValidationException
		 */
		public function testDeleteEntity() {
			$this->expectException(EntityNotFoundException::class);
			$user = null;
			try {
				$user = $this->entityManager->collection('u', UserSchema::class)->entity('u', 'Me, The Best User Ever!');
			} catch (EntityNotFoundException $exception) {
				self::fail('Entity has not been found!');
			}
			$user->delete();
			$user->save();
			$this->entityManager->collection('u', UserSchema::class)->entity('u', 'Me, The Best User Ever!');
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws EntityNotFoundException
		 * @throws InvalidRelationException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownAliasException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws UnknownTableException
		 * @throws ValidationException
		 */
		public function testEntityDetach() {
			$this->schemaManager->load(UserRoleSchema::class);
			$user = $this->entityManager->create(UserSchema::class, [
				'name'    => 'foo-user',
				'email'   => 'foo@user.com',
				'created' => new DateTime(),
			]);
			$root = $this->entityManager->collection('r', RoleSchema::alias)->entity('r', 'root');
			$guest = $this->entityManager->collection('r', RoleSchema::alias)->entity('r', 'guest');
			$user->attach($root);
			$user->attach($guest);
			$user->save();
			$roles = ['guest', 'root'];
			$current = [];
			foreach ($user->join('r', RoleSchema::class) as $record) {
				$entity = $record->getEntity('r');
				self::assertSame(RoleSchema::class, $entity->getSchema()->getName());
				$current[] = $entity->get('name');
			}
			sort($roles);
			sort($current);
			self::assertSame($roles, $current);
			$roles = ['guest'];
			$current = [];
			$user->detach($root);
			$user->save();
			foreach ($user->join('r', RoleSchema::class) as $record) {
				$entity = $record->getEntity('r');
				self::assertSame(RoleSchema::class, $entity->getSchema()->getName());
				$current[] = $entity->get('name');
			}
			sort($roles);
			sort($current);
			self::assertSame($roles, $current);
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws EntityNotFoundException
		 * @throws InvalidRelationException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownAliasException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws UnknownTableException
		 * @throws ValidationException
		 */
		public function testDisconnect() {
			$this->schemaManager->load(UserRoleSchema::class);
			$user = $this->entityManager->create(UserSchema::class, [
				'name'    => 'mrdka',
				'email'   => 'mrdka@mrdka.mrdka',
				'created' => new DateTime(),
			]);
			$root = $this->entityManager->collection('r', RoleSchema::alias)->entity('r', 'root');
			$guest = $this->entityManager->collection('r', RoleSchema::alias)->entity('r', 'guest');
			$user->attach($root);
			$user->attach($root);
			$user->attach($root);
			$user->attach($guest);
			$user->save();
			$roles = ['guest', 'root', 'root', 'root'];
			$current = [];
			foreach ($user->join('r', RoleSchema::class) as $record) {
				$entity = $record->getEntity('r');
				self::assertSame(RoleSchema::class, $entity->getSchema()->getName());
				$current[] = $entity->get('name');
			}
			sort($roles);
			sort($current);
			self::assertSame($roles, $current);
			$current = [];
			$user->disconnect(RoleSchema::class);
			$user->save();
			foreach ($user->join('r', RoleSchema::class) as $record) {
				$entity = $record->getEntity('r');
				self::assertSame(RoleSchema::class, $entity->getSchema()->getName());
				$current[] = $entity->get('name');
			}
			self::assertEmpty($current, 'disconnect is not working?');
		}

		/**
		 * @throws BatchValidationException
		 * @throws DuplicateEntryException
		 * @throws EntityNotFoundException
		 * @throws InvalidRelationException
		 * @throws RecordException
		 * @throws SchemaException
		 * @throws UnknownAliasException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws UnknownTableException
		 * @throws ValidationException
		 */
		public function testUnlinkRelation() {
			$this->schemaManager->load(UserRoleSchema::class);
			$user = $this->entityManager->create(UserSchema::class, [
				'name'    => 'root-user',
				'email'   => 'root@user.com',
				'created' => new DateTime(),
			]);
			$root = $this->entityManager->collection('r', RoleSchema::alias)->entity('r', 'root');
			$guest = $this->entityManager->collection('r', RoleSchema::alias)->entity('r', 'guest');
			$user->attach($root)->set('enabled', false);
			$user->attach($root)->set('enabled', true);
			$user->attach($guest)->set('enabled', false);
			$user->save();
			$roles = ['guest', 'root', 'root'];
			$current = [];
			foreach ($user->join('r', RoleSchema::class) as $record) {
				$entity = $record->getEntity('r');
				self::assertSame(RoleSchema::class, $entity->getSchema()->getName());
				$current[] = $entity->get('name');
			}
			sort($roles);
			sort($current);
			self::assertSame($roles, $current);
			$user->detach($root)->where('r.enabled', '=', 1);
			$user->save();
			$current = [];
			foreach ($user->join('r', RoleSchema::class)->where('e\r.enabled', '=', 1) as $record) {
				$entity = $record->getEntity('r');
				self::assertSame(RoleSchema::class, $entity->getSchema()->getName());
				$current[] = $entity->get('name');
			}
			self::assertEmpty($current);
		}

		public function testPrepareBenchmark() {
			$this->beforeBenchmark();
			self::assertTrue(true);
		}

		/**
		 * @throws BatchValidationException
		 * @throws DriverException
		 * @throws DuplicateEntryException
		 * @throws ExclusiveTransactionException
		 * @throws NoTransactionException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws UnknownPropertyException
		 * @throws UnknownSchemaException
		 * @throws ValidationException
		 */
		public function testBenchmark() {
			$schemas = [
				PooSchema::class,
				FooSchema::class,
				BarSchema::class,
				FooBarSchema::class,
				BarPooSchema::class,
			];
			foreach ($schemas as $name) {
				try {
					$this->storage->execute(new CreateSchemaQuery($this->schemaManager->load($name)));
				} catch (DuplicateTableException $exception) {
				}
			}
			$this->schemaManager->load(FooBarSchema::class);
			$this->schemaManager->load(BarPooSchema::class);
			$this->storage->start();
			$start = microtime(true);
			for ($i = 0; $i < $this->getBenchmarkLimit(); $i++) {
				$foo = $this->entityManager->create(FooSchema::class, [
					'name' => 'foo #' . $i,
				]);
				$poo = $this->entityManager->create(PooSchema::class, [
					'name'  => 'poo of foo $' . $i,
					'label' => "and it's labeled #$i",
				]);
				$bar = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar #' . $i,
				]);
				$bar2 = $this->entityManager->create(BarSchema::class, [
					'name' => 'bar 2 #' . $i,
				]);
				$foo->linkTo($poo);
				$foo->attach($bar);
				$foo->attach($bar2);
				$bar->linkTo($poo);
				$foo->save();
			}
			$this->storage->commit();
			$sum = (microtime(true) - $start);
			$item = ($sum / $i) * 1000;
			$limit = $this->getEntityTimeLimit();
			self::assertLessThanOrEqual($limit, $item, sprintf("[%s] %.2fs, %.2f ms/operation (%.2f%% of %dms limit)", static::class, $sum, $item, (100 * $item) / $limit, $limit));
		}

		protected function beforeBenchmark() {
		}

		protected function getBenchmarkLimit(): int {
			return 250;
		}

		abstract protected function getEntityTimeLimit(): float;
	}
