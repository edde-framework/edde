<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Base64Schema;
	use DateTime;
	use DefaultFloatValueSchema;
	use Edde\Container\ContainerException;
	use Edde\Hydrator\AbstractHydrator;
	use Edde\Schema\SchemaException;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Security\RandomService;
	use Edde\Service\Storage\Storage;
	use Edde\TestCase;
	use Edde\Transaction\TransactionException;
	use Edde\Validator\ValidatorException;
	use IssueSchema;
	use JsonSchema;
	use LabelSchema;
	use ProjectMemberSchema;
	use ProjectSchema;
	use ShittyTypeSchema;
	use Throwable;
	use UserSchema;
	use VoidSchema;
	use function sort;

	abstract class AbstractStorageTest extends TestCase {
		use SchemaManager;
		use Container;
		use Storage;
		use SchemaManager;
		use RandomService;

		public function testPrepareDatabase() {
		}

		/**
		 * @throws StorageException
		 *
		 * @depends testPrepareDatabase
		 */
		public function testCreateSchema() {
			$this->storage->creates([
				LabelSchema::class,
				UserSchema::class,
				ProjectSchema::class,
				ProjectMemberSchema::class,
				IssueSchema::class,
				DefaultFloatValueSchema::class,
				JsonSchema::class,
				Base64Schema::class,
			]);
			self::assertTrue(true, 'everything is ok');
		}

		/**
		 * @throws StorageException
		 */
		public function testQueryException() {
			$this->expectException(StorageException::class);
			$this->expectExceptionMessage('Cannot translate unknown alias [kaboom] to schema name.');
			$this->storage->query('SELECT kaboom:schema FROM this-will-explode', []);
		}

		/**
		 * @throws TransactionException
		 */
		public function testCommitTransactionException() {
			$this->expectException(TransactionException::class);
			$this->expectExceptionMessage('Cannot commit a transaction - there is no one running!');
			$this->storage->commit();
		}

		/**
		 * @throws TransactionException
		 */
		public function testRollbackTransactionException() {
			$this->expectException(TransactionException::class);
			$this->expectExceptionMessage('Cannot rollback a transaction - there is no one running!');
			$this->storage->rollback();
		}

		/**
		 * @throws StorageException
		 */
		public function testInvalidSchemaType() {
			$this->expectException(StorageException::class);
			$this->expectExceptionMessage('Unknown type [this-type-does-not-exists]');
			$this->storage->create(ShittyTypeSchema::class);
		}

		/**
		 * @depends testCreateSchema
		 *
		 * @throws StorageException
		 */
		public function testDuplicateSchema() {
			$this->expectException(DuplicateTableException::class);
			$this->storage->create(LabelSchema::class);
		}

		/**
		 * @depends testDuplicateSchema
		 *
		 * @throws StorageException
		 */
		public function testNullValueException() {
			$this->expectException(NullValueException::class);
			$this->storage->insert(new Entity(LabelSchema::class, ['uuid' => '11111111111111111111111111111111']), new class() extends AbstractHydrator {
				public function hydrate(array $source) {
					return $source;
				}

				/** @inheritdoc */
				public function input(string $name, array $input): array {
					return $input;
				}

				/** @inheritdoc */
				public function update(string $name, array $update): array {
					return $update;
				}

				/** @inheritdoc */
				public function output(string $name, array $output): array {
					return $output;
				}
			});
		}

		/**
		 * @depends testNullValueException
		 *
		 * @throws StorageException
		 * @throws UnknownTableException
		 *
		 * @depends testDuplicateSchema
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
		 * @depends testCollectionSimpleValue
		 *
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
		 * @depends testSimpleCollection
		 *
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testInsertNoTable() {
			self::expectException(UnknownTableException::class);
			$this->schemaManager->load(VoidSchema::class);
			$this->storage->insert(new Entity(VoidSchema::class, []));
		}

		/**
		 * @depends testInsertNoTable
		 *
		 * @throws StorageException
		 */
		public function testValidator() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Value [LabelSchema::name] is not string.');
			$this->storage->insert(new Entity(LabelSchema::class, ['name' => true]));
		}

		/**
		 * @depends testValidator
		 *
		 * @throws TransactionException
		 */
		public function testInsert() {
			$this->storage->transaction(function () {
				$insert = $this->storage->insert(new Entity(LabelSchema::class, [
					'name' => 'this entity is new',
				]));
				self::assertArrayHasKey('uuid', $insert);
				self::assertArrayHasKey('system', $insert);
				self::assertNotNull($insert['uuid']);
				self::assertFalse($insert['system']);
			});
		}

		/**
		 * @depends testInsert
		 *
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
		 * @depends testInsertException2
		 *
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
		 * @depends testInsertUnique
		 *
		 * @throws Throwable
		 */
		public function testSaveUnique() {
			$this->expectException(DuplicateEntryException::class);
			try {
				$this->storage->transaction(function () {
					$this->storage->save(new Entity(LabelSchema::class, [
						'name'   => 'unique2',
						'system' => true,
					]));
					$this->storage->save(new Entity(LabelSchema::class, [
						'name' => 'unique2',
					]));
				});
			} catch (Throwable $exception) {
				throw $exception->getPrevious();
			}
		}

		/**
		 * @depends testSaveUnique
		 *
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
		 * @depends testCollection
		 *
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
		 * @depends testUpdateException
		 *
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
		 * @depends testUpdate
		 *
		 * @throws StorageException
		 * @throws EmptyEntityException
		 */
		public function testSave() {
			$save = $this->storage->save(new Entity(ProjectSchema::class, [
				'name'    => 'another-some-project-here',
				'created' => new DateTime(),
				'start'   => new DateTime($start = '2016-05-24T14:43:11'),
				'end'     => new DateTime(),
			]));
			$save['end'] = null;
			$this->storage->save($save);
			$actual = $this->storage->load(ProjectSchema::class, $save['uuid']);
			self::assertArrayHasKey('end', $actual);
			self::assertEmpty($actual['end']);
			self::assertArrayHasKey('start', $actual);
			self::assertInstanceOf(DateTime::class, $actual['start']);
			self::assertEquals('2016-05-24T14:43:11.000000', $actual['start']->format('Y-m-d\TH:i:s.u'));
		}

		/**
		 * @depends testSave
		 *
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testAttachNotSavedException() {
			$this->expectException(StorageException::class);
			$this->expectExceptionMessage('Source [ProjectSchema] uuid [22222222-2222-4222-8222-222222222222], target [UserSchema] uuid [33333333-3333-4333-9333-333333333333] or both are not saved.');
			$project = $this->storage->save(new Entity(ProjectSchema::class, [
				'name' => 'to be linked',
				'uuid' => '22222222-2222-4222-8222-222222222222',
			]));
			$relation = $this->storage->attach(
				$project,
				new Entity(UserSchema::class, ['uuid' => '33333333-3333-4333-9333-333333333333']),
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
		 * @throws EmptyEntityException
		 */
		public function testAttach() {
			$project = $this->storage->save(new Entity(ProjectSchema::class, [
				'uuid' => '12121212-1212-4212-8212-121212212121',
				'name' => 'to be linked',
			]));
			$user = $this->storage->save(new Entity(UserSchema::class, [
				'uuid'     => '21212121-2121-4121-8121-112121212121',
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
		 * @throws EmptyEntityException
		 */
		public function testAttachInsertUpdate() {
			$relation = $this->storage->attach(
				new Entity(ProjectSchema::class, ['uuid' => '12121212-1212-4212-8212-121212212121']),
				new Entity(UserSchema::class, ['uuid' => '21212121-2121-4121-8121-112121212121']),
				ProjectMemberSchema::class
			);
			$relation['uuid'] = '34567801-5423-4234-8255-014323531243';
			$relation['owner'] = true;
			$this->storage->insert($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, '34567801-5423-4234-8255-014323531243');
			self::assertTrue($relation['owner']);
			self::assertEquals($relation['project'], '12121212-1212-4212-8212-121212212121');
			self::assertEquals($relation['user'], '21212121-2121-4121-8121-112121212121');
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
				new Entity(UserSchema::class, ['uuid' => '21212121-2121-4121-8121-112121212121']),
				new Entity(ProjectSchema::class, ['uuid' => '12121212-1212-4212-8212-121212212121']),
				ProjectMemberSchema::class
			);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws EmptyEntityException
		 */
		public function testUnlink() {
			$this->expectException(EmptyEntityException::class);
			$this->expectExceptionMessage('Requested unknown uuid [34567801-5423-1234-5255-014323531243] of [ProjectMemberSchema].');
			$this->storage->unlink(
				new Entity(ProjectSchema::class, ['uuid' => '12121212-1212-1212-1212-121212212121']),
				new Entity(UserSchema::class, ['uuid' => '21212121-2121-4121-8121-112121212121']),
				ProjectMemberSchema::class
			);
			$this->storage->load(ProjectMemberSchema::class, '34567801-5423-1234-5255-014323531243');
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testLinkException() {
			$this->expectException(SchemaException::class);
			$this->expectExceptionMessage('Invalid relation (!UserSchema)-[ProjectMemberSchema]->(ProjectSchema): Source schema [UserSchema] differs from expected relation [ProjectSchema]; did you swap $source and $target schema?');
			$this->storage->link(
				new Entity(UserSchema::class, ['uuid' => '21212121-2121-4121-8121-112121212121']),
				new Entity(ProjectSchema::class, ['uuid' => '12121212-1212-1212-1212-121212212121']),
				ProjectMemberSchema::class
			);
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws EmptyEntityException
		 */
		public function testLink() {
			$this->expectException(EmptyEntityException::class);
			$this->expectExceptionMessage('Requested unknown uuid [34567801-5255-4234-8423-014323531243] of [ProjectMemberSchema].');
			$project = $this->storage->save(new Entity(ProjectSchema::class, [
				'uuid' => '98743242-3456-4893-8423-324598542154',
				'name' => 'multilink, yaay',
			]));
			$original = $this->storage->attach($project, $user = new Entity(UserSchema::class, ['uuid' => '21212121-2121-4121-8121-112121212121']), ProjectMemberSchema::class);
			$original['uuid'] = '34567801-5255-4234-8423-014323531243';
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
		 * @throws EmptyEntityException
		 */
		public function testDelete() {
			$this->expectException(EmptyEntityException::class);
			$this->expectExceptionMessage('Requested unknown uuid [e5fb371a-9d5e-4cac-9739-fdc7d0ce91df] of [ProjectSchema].');
			$project = $this->storage->save(new Entity(ProjectSchema::class, [
				'uuid' => 'e5fb371a-9d5e-4cac-9739-fdc7d0ce91df',
				'name' => 'kill me, bitch!',
			]));
			$this->storage->delete($project);
			$this->storage->load(ProjectSchema::class, 'e5fb371a-9d5e-4cac-9739-fdc7d0ce91df');
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testQuery() {
			$user = $this->storage->save(new Entity(UserSchema::class, [
				'uuid'     => '54b94349-b304-4bfa-aaec-cd2c6b49aa99',
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
			self::assertSame(4, $count);
		}

		/**
		 * @throws StorageException
		 */
		public function testDefaultFloat() {
			$entity = $this->storage->save(new Entity(DefaultFloatValueSchema::class, [
				'value' => null,
			]));
			self::assertNull($entity['value']);
			$entity = $this->storage->save(new Entity(DefaultFloatValueSchema::class, []));
			self::assertEquals(0, $entity['value']);
		}

		/**
		 * @throws EmptyEntityException
		 * @throws StorageException
		 */
		public function testUtf() {
			$label = $this->storage->save(new Entity(LabelSchema::class, [
				'name' => $name = 'ěščřžýáíéňďť',
			]));
			$label = $this->storage->load(LabelSchema::class, $label['uuid']);
			self::assertSame($name, $label['name']);
		}

		public function testJson() {
			$entity = $this->storage->save(new Entity(JsonSchema::class, [
				'someJson' => $source = (object)[
					'foo'  => [
						(object)['bar' => 1],
						(object)['bar' => 2],
					],
					'bool' => "could be true, but it's a string!",
				],
			]));
			$entity2 = $this->storage->load(JsonSchema::class, $entity['uuid']);
			self::assertEquals($entity, $entity2);
		}

		public function testBase64() {
			$entity = $this->storage->save(new Entity(Base64Schema::class, [
				'someBinary' => $binary = $this->randomService->bytes(256),
			]));
			$entity2 = $this->storage->load(Base64Schema::class, $entity['uuid']);
			self::assertEquals($entity, $entity2);
		}

		/**
		 * @throws ContainerException
		 * @throws SchemaException
		 */
		protected function setUp() {
			parent::setUp();
			$this->schemaManager->loads([
				LabelSchema::class,
				ProjectSchema::class,
				UserSchema::class,
				ProjectMemberSchema::class,
				IssueSchema::class,
				ShittyTypeSchema::class,
				DefaultFloatValueSchema::class,
				JsonSchema::class,
				Base64Schema::class,
			]);
		}
	}
