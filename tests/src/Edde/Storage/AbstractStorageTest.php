<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use DateTime;
	use Edde\Container\ContainerException;
	use Edde\Schema\SchemaException;
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
	use function sort;

	abstract class AbstractStorageTest extends TestCase {
		use SchemaManager;
		use Container;
		use Storage;
		use SchemaManager;

		/**
		 * @throws StorageException
		 */
		public function testCreateSchema() {
			$this->storage->creates([
				LabelSchema::class,
				UserSchema::class,
				ProjectSchema::class,
				ProjectMemberSchema::class,
				OrganizationSchema::class,
				ProjectOrganizationSchema::class,
				IssueSchema::class,
				ToBeOrdered::class,
				ProjectLabelSchema::class,
				IssueProjectSchema::class,
			]);
			self::assertTrue(true, 'everything is ok');
		}

		/**
		 * @throws StorageException
		 * @throws UnknownTableException
		 */
		public function testCollectionSimpleValue() {
			$this->storage->inserts([
				new Entity(ProjectSchema::class, [
					'name' => 'project-01',
				]),
				new Entity(ProjectSchema::class, [
					'name'   => 'project-02',
					'status' => 1,
				]),
			]);
			$record = null;
			foreach ($this->storage->value('SELECT COUNT(*) FROM project') as $record) {
				break;
			}
			self::assertEquals(2, $record);
		}

		/**
		 * @throws StorageException
		 * @throws UnknownTableException
		 */
		public function testSimpleCollection() {
			$actual = [];
			foreach ($this->storage->schema(ProjectSchema::class, 'SELECT * FROM project ORDER BY name') as $entity) {
				self::assertInstanceOf(IEntity::class, $entity);
				unset($entity['uuid'], $entity['created']);
				$actual[] = $entity->toArray();
			}
			self::assertSame([
				[
					'name'   => 'project-01',
					'status' => 0,
					'start'  => null,
					'end'    => null,
				],
				[
					'name'   => 'project-02',
					'status' => 1,
					'start'  => null,
					'end'    => null,
				],
			], $actual);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testInsertNoTable() {
			self::expectException(UnknownTableException::class);
			$this->schemaManager->load(VoidSchema::class);
			$this->storage->insert(new Entity(VoidSchema::class, []));
		}

		/**
		 * @throws StorageException
		 */
		public function testValidator() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Value [LabelSchema::name] is not string.');
			$this->storage->insert(new Entity(LabelSchema::class, ['name' => true]));
		}

		/**
		 * @throws StorageException
		 */
		public function testInsert() {
			$insert = $this->storage->insert(new Entity(LabelSchema::class, [
				'name' => 'this entity is new',
			]));
			self::assertArrayHasKey('uuid', $insert);
			self::assertArrayHasKey('system', $insert);
			self::assertNotNull($insert['uuid']);
			self::assertFalse($insert['system']);
		}

		/**
		 * @throws StorageException
		 */
		public function testInsertException2() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Required value [LabelSchema::name] is null.');
			$this->storage->insert(new Entity(LabelSchema::class, [
				'name' => null,
			]));
		}

		/**
		 * @throws StorageException
		 */
		public function testInsertUnique() {
			$this->expectException(DuplicateEntryException::class);
			$this->storage->inserts([
				new Entity(LabelSchema::class, [
					'name'   => 'unique',
					'system' => true,
				]),
				new Entity(LabelSchema::class, [
					'name' => 'unique',
				]),
			]);
		}

		/**
		 * @throws StorageException
		 */
		public function testCollection() {
			$entities = [];
			foreach ($this->storage->schema(LabelSchema::class, 'select * from label') as $entity) {
				self::assertInstanceOf(IEntity::class, $entity);
				unset($entity['uuid']);
				$entities[] = $entity->toArray();
			}
			sort($entities);
			self::assertEquals([
				[
					'name'   => 'this entity is new',
					'system' => false,
				],
				[
					'name'   => 'unique',
					'system' => true,
				],
			], $entities);
		}

		/**
		 * @throws StorageException
		 */
		public function testUpdateException() {
			$this->expectException(StorageException::class);
			$this->expectExceptionMessage('Missing primary key [uuid] for update!');
			$insert = $this->storage->insert(new Entity(ProjectSchema::class, [
				'name'    => 'some-project-here',
				'created' => new DateTime(),
				'start'   => new DateTime(),
				'end'     => new DateTime(),
			]));
			unset($insert['uuid']);
			$this->storage->update($insert);
		}

		/**
		 * @throws StorageException
		 */
		public function testUpdate() {
			$insert = $this->storage->insert(new Entity(ProjectSchema::class, [
				'name'    => 'some-project-here',
				'created' => new DateTime(),
				'start'   => new DateTime(),
				'end'     => new DateTime(),
			]));
			$insert['end'] = null;
			$this->storage->update($insert);
			$actual = $this->storage->load(ProjectSchema::class, $insert['uuid']);
			self::assertInstanceOf(IEntity::class, $actual);
			self::assertArrayHasKey('end', $actual);
			self::assertEmpty($actual['end']);
			self::assertArrayHasKey('start', $actual);
			self::assertInstanceOf(DateTime::class, $actual['start']);
		}

		/**
		 * @throws StorageException
		 * @throws UnknownUuidException
		 */
		public function testSave() {
			$save = $this->storage->save(new Entity(ProjectSchema::class, [
				'name'    => 'another-some-project-here',
				'created' => new DateTime(),
				'start'   => new DateTime(),
				'end'     => new DateTime(),
			]));
			$save['end'] = null;
			$this->storage->save($save);
			$actual = $this->storage->load(ProjectSchema::class, $save['uuid']);
			self::assertArrayHasKey('end', $actual);
			self::assertEmpty($actual['end']);
			self::assertArrayHasKey('start', $actual);
			self::assertInstanceOf(DateTime::class, $actual['start']);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testAttachNotSavedException() {
			$this->expectException(StorageException::class);
			$this->expectExceptionMessage('Source [ProjectSchema] uuid [yaay], target [UserSchema] uuid [nope] or both are not saved.');
			$project = $this->storage->save(new Entity(ProjectSchema::class, [
				'name' => 'to be linked',
				'uuid' => 'yaay',
			]));
			$relation = $this->storage->attach(
				$project,
				new Entity(UserSchema::class, ['uuid' => 'nope']),
				ProjectMemberSchema::class
			);
			$relation['owner'] = true;
			$this->storage->save($relation);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testAttachException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Invalid relation (!UserSchema)-[ProjectMemberSchema]->(ProjectSchema): Source schema [UserSchema] differs from expected relation [ProjectSchema]; did you swap $source and $target schema?');
			$user = $this->storage->save(new Entity(UserSchema::class, [
				'login'    => 'root',
				'password' => '123',
			]));
			$project = $this->storage->save(new Entity(ProjectSchema::class, [
				'name' => 'to be linked',
			]));
			$relation = $this->storage->attach(
				$user,
				$project,
				ProjectMemberSchema::class
			);
			$relation['owner'] = true;
			$this->storage->save($relation);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws UnknownUuidException
		 */
		public function testAttach() {
			$project = $this->storage->save(new Entity(ProjectSchema::class, [
				'uuid' => 'one',
				'name' => 'to be linked',
			]));
			$user = $this->storage->save(new Entity(UserSchema::class, [
				'uuid'     => 'two',
				'login'    => 'roota',
				'password' => '123',
			]));
			$relation = $this->storage->attach($project, $user, ProjectMemberSchema::class);
			$relation['owner'] = true;
			$relation = $this->storage->save($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, $relation['uuid']);
			self::assertTrue($relation['owner']);
			self::assertEquals($relation['project'], $project['uuid']);
			self::assertEquals($relation['user'], $user['uuid']);
			$relation['owner'] = false;
			$this->storage->save($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, $relation['uuid']);
			self::assertFalse($relation['owner']);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws UnknownUuidException
		 */
		public function testAttachInsertUpdate() {
			$relation = $this->storage->attach(
				new Entity(ProjectSchema::class, ['uuid' => 'one']),
				new Entity(UserSchema::class, ['uuid' => 'two']),
				ProjectMemberSchema::class
			);
			$relation['uuid'] = 'relation';
			$relation['owner'] = true;
			$this->storage->insert($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, 'relation');
			self::assertTrue($relation['owner']);
			self::assertEquals($relation['project'], 'one');
			self::assertEquals($relation['user'], 'two');
			$relation['owner'] = false;
			$this->storage->update($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, $relation['uuid']);
			self::assertFalse($relation['owner']);
			$relation['owner'] = true;
			$this->storage->update($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, $relation['uuid']);
			self::assertTrue($relation['owner']);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testUnlinkException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Invalid relation (!UserSchema)-[ProjectMemberSchema]->(ProjectSchema): Source schema [UserSchema] differs from expected relation [ProjectSchema]; did you swap $source and $target schema?');
			$this->storage->unlink(
				new Entity(UserSchema::class, ['uuid' => 'two']),
				new Entity(ProjectSchema::class, ['uuid' => 'one']),
				ProjectMemberSchema::class
			);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws UnknownUuidException
		 */
		public function testUnlink() {
			$this->expectException(UnknownUuidException::class);
			$this->expectExceptionMessage('Requested unknown uuid [relation] of [ProjectMemberSchema].');
			$this->storage->unlink(
				new Entity(ProjectSchema::class, ['uuid' => 'one']),
				new Entity(UserSchema::class, ['uuid' => 'two']),
				ProjectMemberSchema::class
			);
			$this->storage->load(ProjectMemberSchema::class, 'relation');
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testLinkException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Invalid relation (!UserSchema)-[ProjectMemberSchema]->(ProjectSchema): Source schema [UserSchema] differs from expected relation [ProjectSchema]; did you swap $source and $target schema?');
			$this->storage->link(
				new Entity(UserSchema::class, ['uuid' => 'two']),
				new Entity(ProjectSchema::class, ['uuid' => 'one']),
				ProjectMemberSchema::class
			);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws UnknownUuidException
		 */
		public function testLink() {
			$this->expectException(UnknownUuidException::class);
			$this->expectExceptionMessage('Requested unknown uuid [original] of [ProjectMemberSchema].');
			$project = $this->storage->save(new Entity(ProjectSchema::class, [
				'uuid' => 'two-pi',
				'name' => 'multilink, yaay',
			]));
			$original = $this->storage->attach($project, $user = new Entity(UserSchema::class, ['uuid' => 'two']), ProjectMemberSchema::class);
			$original['uuid'] = 'original';
			$this->storage->save($original);
			$relation = $this->storage->link(
				$project,
				$user,
				ProjectMemberSchema::class
			);
			$this->storage->save($relation);
			$this->storage->load(ProjectMemberSchema::class, $original['uuid']);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws UnknownUuidException
		 */
		public function testDelete() {
			$this->expectException(UnknownUuidException::class);
			$this->expectExceptionMessage('Requested unknown uuid [to-be-deleted] of [ProjectSchema].');
			$project = $this->storage->save(new Entity(ProjectSchema::class, [
				'uuid' => 'to-be-deleted',
				'name' => 'kill me, bitch!',
			]));
			$this->storage->delete($project);
			$this->storage->load(ProjectSchema::class, 'to-be-deleted');
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testQuery() {
			$user = $this->storage->save(new Entity(UserSchema::class, [
				'uuid'     => 'ja',
				'login'    => 'me',
				'password' => '1234',
			]));
			$project = $this->storage->save(new Entity(ProjectSchema::class, [
				'name' => 'project',
			]));
			$this->storage->save(new Entity(LabelSchema::class, [
				'name' => 'lejbl',
			]));
			$relation = $this->storage->attach($project, $user, ProjectMemberSchema::class);
			$relation['owner'] = true;
			$this->storage->save($relation);
			$query = '
				SELECT
					COUNT(*) 
				FROM 
					u:schema u:delimit, 
					p:schema p, 
					up:schema pu:delimit
				WHERE
					pu.project = p.uuid AND
					pu.user = u.uuid
			';
			$params = [
				'$query' => [
					'u'  => UserSchema::class,
					'p'  => ProjectSchema::class,
					'up' => ProjectMemberSchema::class,
				],
			];
			$count = 0;
			foreach ($this->storage->value($query, $params) as $count) {
				break;
			}
			self::assertSame(2, $count);
		}

		/**
		 * @throws ContainerException
		 * @throws SchemaException
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
