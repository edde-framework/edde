<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use DateTime;
	use Edde\Collection\CollectionException;
	use Edde\Collection\EntityNotFoundException;
	use Edde\Collection\IEntity;
	use Edde\Container\ContainerException;
	use Edde\Query\SelectQuery;
	use Edde\Schema\SchemaException;
	use Edde\Schema\SchemaValidationException;
	use Edde\Service\Collection\CollectionManager;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\TestCase;
	use Edde\Validator\ValidatorException;
	use LabelSchema;
	use VoidSchema;
	use function property_exists;

	abstract class AbstractStorageTest extends TestCase {
		use SchemaManager;
		use Container;
		use Storage;
		use CollectionManager;

		/**
		 * @throws StorageException
		 */
		public function testCreateSchema() {
			$schemas = [
				LabelSchema::class,
			];
			foreach ($schemas as $schema) {
				$this->storage->create($schema);
			}
			self::assertTrue(true, 'everything is ok');
		}

		/**
		 * @throws StorageException
		 */
		public function testInsertNoTable() {
			self::expectException(UnknownTableException::class);
			self::expectExceptionMessage('SQLSTATE[42S02]: Base table or view not found: 1146 Table \'edde.VoidSchema\' doesn\'t exist');
			$this->storage->insert(VoidSchema::class, (object)[]);
		}

		/**
		 * @throws StorageException
		 */
		public function testValidator() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Value [LabelSchema::name] is not string.');
			$this->storage->insert(LabelSchema::class, (object)['name' => true]);
		}

		/**
		 * @throws StorageException
		 */
		public function testInsert() {
			$source = $this->storage->insert(LabelSchema::class, (object)[
				'name' => 'this entity is new',
			]);
			self::assertTrue(property_exists($source, 'uuid'));
			self::assertTrue(property_exists($source, 'system'));
			self::assertNotEmpty($source->uuid);
			self::assertFalse($source->system);
		}

		/**
		 * @throws StorageException
		 */
		public function testInsertException2() {
			$this->expectException(SchemaValidationException::class);
			$this->expectExceptionMessage('Validation of schema [SimpleSchema] failed.');
			$this->storage->insert(SimpleSchema::class, (object)[
				'name' => null,
			]);
		}

		/**
		 * @throws StorageException
		 */
		public function testInsertUnique() {
			$this->expectException(DuplicateEntryException::class);
			$this->storage->insert(SimpleSchema::class, (object)[
				'name' => 'unique',
			]);
			$this->storage->insert(SimpleSchema::class, (object)[
				'name' => 'unique',
			]);
		}

		/**
		 * @throws StorageException
		 */
		public function testSave() {
			$object = $this->storage->insert(SimpleSchema::class, (object)[
				'name'     => 'some name for this entity',
				'optional' => 'this string is optional, but I wanna fill it!',
			]);
			self::assertTrue(property_exists($object, 'uuid'));
			self::assertNotEmpty($object->uuid);
			$object = $this->storage->insert(SimpleSchema::class, (object)[
				'name'     => 'another name',
				'optional' => null,
			]);
			self::assertTrue(property_exists($object, 'uuid'));
			self::assertNotEmpty($object->uuid);
		}

		/**
		 * @throws CollectionException
		 * @throws \Edde\Collection\EntityException
		 * @throws SchemaException
		 */
		public function testCollection() {
			$collection = $this->collectionManager->collection();
			$collection->use(SimpleSchema::class);
			$entities = [];
			foreach ($collection as $record) {
				$entity = $record->getEntity(SimpleSchema::class)->toObject();
				unset($entity->uuid);
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
		 * @throws SchemaException
		 * @throws ContainerException
		 */
		public function testUpdate() {
			$entity = $this->entityManager->entity(SimpleSchema::class, [
				'name'     => 'to-be-updated',
				'optional' => 'this is a new nice and updated string',
				'value'    => 3.14,
				'date'     => new DateTime('24.12.2020 12:24:13'),
				'question' => false,
			])->commit();
			$entity->set('optional', null);
			$expect = $entity->toArray();
			$entity->commit();
			$entity = $this->entityManager->collection('c', SimpleSchema::class)->entity('c', $entity->get('uuid'));
			self::assertFalse($entity->isDirty(), 'entity should NOT be dirty right after load!');
			self::assertEquals($expect, $array = $entity->toArray());
			self::assertTrue(($type = gettype($array['value'])) === 'double', 'value [' . $type . '] is not float!');
			self::assertInstanceOf(DateTime::class, $array['date']);
			self::assertTrue(($type = gettype($array['question'])) === 'boolean', 'question [' . $type . '] is not bool!');
			self::assertFalse($array['question']);
		}

		/**
		 * @throws SchemaException
		 * @throws ContainerException
		 */
		public function testLink() {
			$foo = $this->entityManager->entity(FooSchema::class, [
				'name'  => 'foo with poo',
				'label' => 'I wanna have a label on this one',
			]);
			$poo = $this->entityManager->entity(PooSchema::class, [
				'name'  => 'the name of this epic Poo!',
				'label' => 'smells like Hell',
			]);
			$anotherPoo = $this->entityManager->entity(PooSchema::class, [
				'name' => 'this is another poo!',
			]);
			$foo->linkTo($poo);
			$foo->linkTo($anotherPoo);
			$foo->linkTo($poo);
			$foo->commit();
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
		 * @throws SchemaException
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
		 * @throws SchemaException
		 * @throws ContainerException
		 */
		public function testRelationTo() {
			$foo = $this->entityManager->entity(FooSchema::class, [
				'name' => 'foo The First',
			]);
			$foo2 = $this->entityManager->entity(FooSchema::class, [
				'name' => 'foo The Second',
			]);
			$bar = $this->entityManager->entity(BarSchema::class, [
				'name' => 'bar The Second',
			]);
			$bar2 = $this->entityManager->entity(BarSchema::class, [
				'name' => 'bar The Third',
			]);
			$bar3 = $this->entityManager->entity(BarSchema::class, [
				'name' => 'Bar for The Foo',
			]);
			$this->entityManager->entity(BarSchema::class, [
				'name' => 'Another, very secret bar!',
			]);
			$poo = $this->entityManager->entity(PooSchema::class, [
				'name' => 'Da Poo The First One!',
			]);
			$poo2 = $this->entityManager->entity(PooSchema::class, [
				'name' => 'Da Poo The Bigger One!',
			]);
			$poo3 = $this->entityManager->entity(PooSchema::class, [
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
			$foo->commit();
			$foo2->commit();
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
		 * @throws SchemaException
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
		 * @throws SchemaException
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
		 * @throws SchemaException
		 * @throws ContainerException
		 */
		public function testRelationAttribute() {
			$this->schemaManager->load(UserRoleSchema::class);
			$user = $this->entityManager->entity(UserSchema::class, [
				'name'    => 'Me, The Best User Ever!',
				'email'   => 'me@there.here',
				'created' => new DateTime(),
			]);
			$root = $this->entityManager->entity(RoleSchema::class, [
				'name'  => 'root',
				'label' => 'he can do everything!',
			]);
			$guest = $this->entityManager->entity(RoleSchema::class, [
				'name'  => 'guest',
				'label' => 'this one can do almost nothing!',
			]);
			$user->attach($root)->set('enabled', false);
			$user->attach($root)->set('enabled', true);
			$user->attach($guest)->set('enabled', false);
			$user->commit();
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
		 * @throws SchemaException
		 * @throws ContainerException
		 */
		public function testMoreRelations() {
			$this->schemaManager->load(SourceOneTargetSchema::class);
			$this->schemaManager->load(SourceTwoTargetSchema::class);
			$source = $this->entityManager->entity(SourceSchema::class);
			$target = $this->entityManager->entity(TargetSchema::class);
			$source->set('name', 'source-yapee');
			$target->set('name', 'yapee');
			$source->attach($target, SourceTwoTargetSchema::class);
			$source->commit();
			$current = [];
			foreach ($source->join('t', TargetSchema::class, SourceTwoTargetSchema::class) as $record) {
				$current[] = $record->getEntity('t')->get('name');
			}
			self::assertEquals(['yapee'], $current);
			self::assertSame(0, $source->join('t', TargetSchema::class, SourceOneTargetSchema::class)->count('t'));
		}

		/**
		 * @throws SchemaException
		 * @throws ContainerException
		 */
		public function testReverseRelation() {
			$this->schemaManager->load(SourceOneTargetSchema::class);
			$this->schemaManager->load(SourceTwoTargetSchema::class);
			$target = $this->entityManager->entity(TargetSchema::class, ['name' => 'boo'])->commit();
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
		 * @throws SchemaException
		 * @throws ContainerException
		 */
		public function testEntityDetach() {
			$this->schemaManager->load(UserRoleSchema::class);
			$user = $this->entityManager->entity(UserSchema::class, [
				'name'    => 'foo-user',
				'email'   => 'foo@user.com',
				'created' => new DateTime(),
			]);
			$root = $this->entityManager->collection('r', RoleSchema::alias)->entity('r', 'root');
			$guest = $this->entityManager->collection('r', RoleSchema::alias)->entity('r', 'guest');
			$user->attach($root);
			$user->attach($guest);
			$user->commit();
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
			$user->commit();
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
		 * @throws SchemaException
		 * @throws ContainerException
		 */
		public function testDisconnect() {
			$this->schemaManager->load(UserRoleSchema::class);
			$user = $this->entityManager->entity(UserSchema::class, [
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
			$user->commit();
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
			$user->commit();
			foreach ($user->join('r', RoleSchema::class) as $record) {
				$entity = $record->getEntity('r');
				self::assertSame(RoleSchema::class, $entity->getSchema()->getName());
				$current[] = $entity->get('name');
			}
			self::assertEmpty($current, 'disconnect is not working?');
		}

		/**
		 * @throws SchemaException
		 * @throws ContainerException
		 */
		public function testUnlinkRelation() {
			$this->schemaManager->load(UserRoleSchema::class);
			$user = $this->entityManager->entity(UserSchema::class, [
				'name'    => 'root-user',
				'email'   => 'root@user.com',
				'created' => new DateTime(),
			]);
			$root = $this->entityManager->collection('r', RoleSchema::alias)->entity('r', 'root');
			$guest = $this->entityManager->collection('r', RoleSchema::alias)->entity('r', 'guest');
			$user->attach($root)->set('enabled', false);
			$user->attach($root)->set('enabled', true);
			$user->attach($guest)->set('enabled', false);
			$user->commit();
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
			$user->commit();
			$current = [];
			foreach ($user->join('r', RoleSchema::class)->where('e\r.enabled', '=', 1) as $record) {
				$entity = $record->getEntity('r');
				self::assertSame(RoleSchema::class, $entity->getSchema()->getName());
				$current[] = $entity->get('name');
			}
			self::assertEmpty($current);
		}

		protected function setUp() {
			parent::setUp();
			$this->schemaManager->loads([
				VoidSchema::class,
				LabelSchema::class,
			]);
		}
	}
