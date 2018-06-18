<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use DateTime;
	use Edde\Collection\EntityNotFoundException;
	use Edde\Container\ContainerException;
	use Edde\Filter\FilterException;
	use Edde\Schema\SchemaException;
	use Edde\Service\Collection\CollectionManager;
	use Edde\Service\Collection\EntityManager;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\TestCase;
	use Edde\Validator\ValidatorException;
	use IssueProjectSchema;
	use IssueSchema;
	use LabelSchema;
	use OrganizationSchema;
	use ProjectLabelSchema;
	use ProjectMemberSchema;
	use ProjectOrganizationSchema;
	use ProjectSchema;
	use ReflectionException;
	use ToBeOrdered;
	use UserSchema;
	use VoidSchema;
	use function shuffle;
	use function sort;

	abstract class AbstractStorageTest extends TestCase {
		use SchemaManager;
		use Container;
		use Storage;
		use CollectionManager;
		use EntityManager;

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function testInsertNoTable() {
			self::expectException(UnknownTableException::class);
			$this->schemaManager->load(VoidSchema::class);
			$this->storage->insert($this->entityManager->entity(VoidSchema::class));
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function testValidator() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Value [LabelSchema::name] is not string.');
			$this->storage->insert($this->entityManager->entity(LabelSchema::class, ['name' => true]));
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function testInsert() {
			$this->storage->insert($entity = $this->entityManager->entity(LabelSchema::class, $object = [
				'name' => 'this entity is new',
			]));
			self::assertNotNull($entity->get('uuid'));
			self::assertFalse($entity->get('system'));
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function testInsertException2() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Required value [LabelSchema::name] is null.');
			$this->storage->insert($this->entityManager->entity(LabelSchema::class, [
				'name' => null,
			]));
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function testInsertUnique() {
			$this->expectException(DuplicateEntryException::class);
			$this->storage->insert($this->entityManager->entity(LabelSchema::class, [
				'name'   => 'unique',
				'system' => true,
			]));
			$this->storage->insert($this->entityManager->entity(LabelSchema::class, [
				'name' => 'unique',
			]));
		}

		/**
		 * @throws FilterException
		 * @throws QueryException
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testCollection() {
			$collection = $this->collectionManager->collection();
			$collection->select(LabelSchema::class);
			$entities = [];
			foreach ($collection->execute() as $record) {
				$entity = $record->getEntity(LabelSchema::class)->toObject();
				self::assertSame($record->getEntity(LabelSchema::class), $record->getEntity(LabelSchema::class));
				unset($entity->uuid);
				$entities[] = $entity;
			}
			sort($entities);
			self::assertEquals([
				(object)[
					'name'   => 'this entity is new',
					'system' => false,
				],
				(object)[
					'name'   => 'unique',
					'system' => true,
				],
			], $entities);
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testUpdate() {
			$this->storage->insert($entity = $this->entityManager->entity(ProjectSchema::class, [
				'name'    => 'some-project-here',
				'created' => new DateTime(),
				'start'   => new DateTime(),
				'end'     => new DateTime(),
			]));
			$entity->set('end', null);
			$this->storage->update($entity);
			$actual = $this->storage->load(ProjectSchema::class, $entity->getPrimary()->get());
			self::assertNull($actual->get('end'));
			self::assertInstanceOf(DateTime::class, $actual->get('start'));
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testSave() {
			$this->storage->save($entity = $this->entityManager->entity(ProjectSchema::class, [
				'name'    => 'another-some-project-here',
				'created' => new DateTime(),
				'start'   => new DateTime(),
				'end'     => new DateTime(),
			]));
			$entity->set('end', null);
			$this->storage->save($entity);
			$actual = $this->storage->load(ProjectSchema::class, $entity->getPrimary()->get());
			self::assertNull($actual->get('end'));
			self::assertInstanceOf(DateTime::class, $actual->get('start'));
		}

		/**
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testAttachException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Invalid relation (!UserSchema)-[ProjectMemberSchema]->(ProjectSchema): Source schema [UserSchema] differs from expected relation [ProjectSchema]; did you swap $source and $target schema?');
			$project = $this->entityManager->entity(ProjectSchema::class, [
				'name' => 'to be linked',
			]);
			$user = $this->entityManager->entity(UserSchema::class, [
				'login'    => 'root',
				'password' => '123',
			]);
			$relation = $this->storage->attach($user, $project, ProjectMemberSchema::class);
			$relation->set('owner', true);
			$this->storage->saves([
				$project,
				$user,
				$relation,
			]);
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testAttach() {
			$project = $this->entityManager->entity(ProjectSchema::class, [
				'uuid' => 'one',
				'name' => 'to be linked',
			]);
			$user = $this->entityManager->entity(UserSchema::class, [
				'uuid'     => 'two',
				'login'    => 'root',
				'password' => '123',
			]);
			$relation = $this->storage->attach($project, $user, ProjectMemberSchema::class);
			$relation->set('owner', true);
			$this->storage->save($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, $relation->get('uuid'));
			self::assertTrue($relation->get('owner'));
			self::assertEquals($relation->get('project'), $project->get('uuid'));
			self::assertEquals($relation->get('user'), $user->get('uuid'));
			$relation->set('owner', false);
			$this->storage->save($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, $relation->get('uuid'));
			self::assertFalse($relation->get('owner'));
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testAttachInsertUpdate() {
			$relation = $this->storage->attach(
				$project = $this->storage->load(ProjectSchema::class, 'one'),
				$user = $this->storage->load(UserSchema::class, 'two'),
				ProjectMemberSchema::class
			);
			$relation->set('uuid', 'relation');
			$relation->set('owner', true);
			$this->storage->insert($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, 'relation');
			self::assertTrue($relation->get('owner'));
			self::assertEquals($relation->get('project'), $project->get('uuid'));
			self::assertEquals($relation->get('user'), $user->get('uuid'));
			$relation->set('owner', false);
			$this->storage->update($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, $relation->get('uuid'));
			self::assertFalse($relation->get('owner'));
			$relation->set('owner', true);
			$this->storage->update($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, $relation->get('uuid'));
			self::assertTrue($relation->get('owner'));
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testUnlinkException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Invalid relation (!UserSchema)-[ProjectMemberSchema]->(ProjectSchema): Source schema [UserSchema] differs from expected relation [ProjectSchema]; did you swap $source and $target schema?');
			$this->storage->unlink(
				$this->storage->load(UserSchema::class, 'two'),
				$this->storage->load(ProjectSchema::class, 'one'),
				ProjectMemberSchema::class
			);
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testUnlink() {
			$this->expectException(EntityNotFoundException::class);
			$this->expectExceptionMessage('Cannot load any entity [ProjectMemberSchema] with id [relation].');
			$this->storage->unlink(
				$project = $this->storage->load(ProjectSchema::class, 'one'),
				$user = $this->storage->load(UserSchema::class, 'two'),
				ProjectMemberSchema::class
			);
			$this->storage->load(ProjectMemberSchema::class, 'relation');
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testLinkException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Invalid relation (!UserSchema)-[ProjectMemberSchema]->(ProjectSchema): Source schema [UserSchema] differs from expected relation [ProjectSchema]; did you swap $source and $target schema?');
			$project = $this->entityManager->entity(ProjectSchema::class, [
				'uuid' => 'two-pi',
				'name' => 'multilink, yaay',
			]);
			$this->storage->link(
				$this->storage->load(UserSchema::class, 'two'),
				$project,
				ProjectMemberSchema::class
			);
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testLink() {
			$this->expectException(EntityNotFoundException::class);
			$this->expectExceptionMessage('Cannot load any entity [ProjectMemberSchema] with id [original].');
			$project = $this->entityManager->entity(ProjectSchema::class, [
				'uuid' => 'two-pi',
				'name' => 'multilink, yaay',
			]);
			$user = $this->storage->load(UserSchema::class, 'two');
			$original = $this->storage->attach($project, $user, ProjectMemberSchema::class);
			$original->set('uuid', 'original');
			$this->storage->save($original);
			$relation = $this->storage->link(
				$project,
				$user,
				ProjectMemberSchema::class
			);
			$this->storage->save($relation);
			$this->storage->load(ProjectMemberSchema::class, $original->get('uuid'));
		}

		/**
		 * @throws EntityNotFoundException
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testDelete() {
			$this->expectException(EntityNotFoundException::class);
			$this->expectExceptionMessage('Cannot load any entity [ProjectSchema] with id [to-be-deleted].');
			$project = $this->entityManager->entity(ProjectSchema::class, [
				'uuid' => 'to-be-deleted',
				'name' => 'kill me, bitch!',
			]);
			$this->storage->save($project);
			$this->storage->delete($project);
			$this->storage->load(ProjectSchema::class, 'to-be-deleted');
		}

		/**
		 * @throws FilterException
		 * @throws QueryException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testQuery() {
			$entities[] = $user = $this->entityManager->entity(UserSchema::class, [
				'uuid'     => 'ja',
				'login'    => 'me',
				'password' => '1234',
			]);
			$entities[] = $project = $this->entityManager->entity(ProjectSchema::class, [
				'name' => 'project',
			]);
			$entities[] = $this->entityManager->entity(LabelSchema::class, [
				'name' => 'lejbl',
			]);
			$entities[] = $relation = $this->storage->attach($project, $user, ProjectMemberSchema::class);
			$relation->set('owner', true);
			$this->storage->saves($entities);
			$collection = $this->collectionManager->collection($query = new Query());
			self::assertSame($collection->getQuery(), $query);
			$collection->selects([
				'u'  => UserSchema::class,
				'p'  => ProjectSchema::class,
				'pm' => ProjectMemberSchema::class,
				'l'  => LabelSchema::class,
			]);
			$count = 0;
			foreach ($collection->execute() as $record) {
				$count++;
				self::assertEquals(UserSchema::class, $record->getEntity('u')->getSchema()->getName());
				self::assertEquals(ProjectSchema::class, $record->getEntity('p')->getSchema()->getName());
				self::assertEquals(ProjectMemberSchema::class, $record->getEntity('pm')->getSchema()->getName());
				self::assertEquals(LabelSchema::class, $record->getEntity('l')->getSchema()->getName());
			}
			self::assertSame(60, $count);
		}

		/**
		 * @throws FilterException
		 * @throws QueryException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testBasicQuery() {
			$entities[] = $user = $this->entityManager->entity(UserSchema::class, [
				'uuid'     => 'on',
				'login'    => 'tadaa',
				'password' => '1234',
			]);
			$entities[] = $project = $this->entityManager->entity(ProjectSchema::class, [
				'name' => 'expected project',
			]);
			$entities[] = $project2 = $this->entityManager->entity(ProjectSchema::class, [
				'name' => 'another - less - expected project',
			]);
			$entities[] = $relation = $this->storage->attach($project, $user, ProjectMemberSchema::class);
			$relation->set('owner', true);
			$entities[] = $this->storage->attach($project2, $user, ProjectMemberSchema::class);
			$this->storage->saves($entities);
			$collection = $this->collectionManager->collection($query = new Query());
			self::assertSame($collection->getQuery(), $query);
			$collection->selects([
				'u'  => UserSchema::class,
				'p'  => ProjectSchema::class,
				'pm' => ProjectMemberSchema::class,
			]);
			$query->attach('p', 'u', 'pm');
			// ... 1. prepare individual wheres which could be used
			$wheres = $query->wheres();
			$wheres->where('project status in')->in('p', 'status');
			$wheres->where('project status not in')->notIn('p', 'status');
			$wheres->where('project status greater')->greaterThan('p', 'status');
			$wheres->where('project status greater equal')->greaterThanEqual('p', 'status');
			$wheres->where('project status lesser')->lesserThan('p', 'status');
			$wheres->where('project status lesser equal')->lesserThanEqual('p', 'status');
			$wheres->where('user uuid')->equalTo('u', 'uuid');
			$wheres->where('is owner')->equalTo('pm', 'owner');
			$wheres->where('project name')->equalTo('p', 'name');
			$wheres->where('project start')->isNull('p', 'start');
			$wheres->where('project name not null')->isNotNull('p', 'name');
			// ... 2. chain them together using operators and groups
			$chains = $wheres->chains();
			$chains->chain('da group')->where('user uuid')->and('is owner')->and('project name');
			$chains->chain('something')->where('project start')->and('project name not null');
			$chains->chain('project status gte')->where('project status greater')->and('project status greater equal');
			$chains->chain('project status lte')->where('project status lesser')->and('project status lesser equal');
			$chains->chain()->where('project status in')
			       ->and('da group')
			       ->and('something')
			       ->and('project status gte')
			       ->and('project status lte')
			       ->and('project status not in');
			// ... 3. set parameters for where based on given or guessed names
			$bind = [
				'project status in'            => (function () {
					yield from [ProjectSchema::STATUS_CREATED, ProjectSchema::STATUS_STARTED];
				})(),
				'project status greater'       => -1,
				'project status greater equal' => 0,
				'project status lesser'        => 1,
				'project status lesser equal'  => 0,
				'user uuid'                    => 'on',
				'is owner'                     => true,
				'project name'                 => 'expected project',
				'project status not in'        => [ProjectSchema::STATUS_ARCHIVED],
			];
			$count = 0;
			foreach ($collection->execute($bind) as $record) {
				$count++;
				$user = $record->getEntity('u');
				self::assertSame('on', $user->get('uuid'));
				self::assertTrue($record->getEntity('pm')->get('owner'));
				self::assertSame('expected project', $record->getEntity('p')->get('name'));
			}
			self::assertEquals(1, $count);
		}

		/**
		 * @throws QueryException
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testCount() {
			self::assertEquals(7, $this->collectionManager->collection()->select(ProjectSchema::class)->count(ProjectSchema::class));
			self::assertEquals(4, $this->collectionManager->collection()->select(ProjectMemberSchema::class)->count(ProjectMemberSchema::class));
		}

		/**
		 * @throws FilterException
		 * @throws QueryException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testOrder() {
			$collection = $this->collectionManager->collection();
			$shuffled = $expected = [
				9.4,
				8.123,
				7.2,
				6.42,
				5.3,
				4.9,
				3.1,
				2.3,
				1.1,
			];
			shuffle($shuffled);
			foreach ($shuffled as $item) {
				$this->storage->insert($this->entityManager->entity(ToBeOrdered::class, [
					'index' => $item,
				]));
			}
			$collection->select(ToBeOrdered::class);
			$collection->order(ToBeOrdered::class, 'index', 'desc');
			$actual = [];
			foreach ($collection->execute() as $record) {
				$actual[] = $record->getEntity(ToBeOrdered::class)->get('index');
			}
			self::assertEquals($expected, $actual);
		}

		/**
		 * @throws FilterException
		 * @throws QueryException
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testPaging() {
			$collection = $this->collectionManager->collection();
			$collection->select(ToBeOrdered::class);
			$collection->order(ToBeOrdered::class, 'index', 'desc');
			$collection->page(1, 3);
			$expected = [
				6.42,
				5.3,
				4.9,
			];
			self::assertEquals(9, $collection->count(ToBeOrdered::class));
			$actual = [];
			foreach ($collection->execute() as $record) {
				$actual[] = $record->getEntity(ToBeOrdered::class)->get('index');
			}
			self::assertEquals($expected, $actual);
			$collection->page(0, 3);
			$expected = [
				9.4,
				8.123,
				7.2,
			];
			$actual = [];
			foreach ($collection->execute() as $record) {
				$actual[] = $record->getEntity(ToBeOrdered::class)->get('index');
			}
			self::assertEquals($expected, $actual);
			$collection->page(2, 3);
			$expected = [
				3.1,
				2.3,
				1.1,
			];
			$actual = [];
			foreach ($collection->execute() as $record) {
				$actual[] = $record->getEntity(ToBeOrdered::class)->get('index');
			}
			self::assertEquals($expected, $actual);
		}

		/**
		 * @throws SchemaException
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$this->schemaManager->loads([
				LabelSchema::class,
				ProjectSchema::class,
				UserSchema::class,
				ProjectMemberSchema::class,
				OrganizationSchema::class,
				ProjectOrganizationSchema::class,
				ToBeOrdered::class,
				IssueSchema::class,
				IssueProjectSchema::class,
				ProjectLabelSchema::class,
			]);
		}
	}
