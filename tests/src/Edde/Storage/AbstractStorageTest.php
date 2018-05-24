<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use DateTime;
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
	use LabelSchema;
	use ProjectMemberSchema;
	use ProjectSchema;
	use ReflectionException;
	use UserSchema;
	use VoidSchema;

	abstract class AbstractStorageTest extends TestCase {
		use SchemaManager;
		use Container;
		use Storage;
		use CollectionManager;
		use EntityManager;

		/**
		 * @throws StorageException
		 */
		public function testCreateSchema() {
			$schemas = [
				LabelSchema::class,
				UserSchema::class,
				ProjectSchema::class,
				ProjectMemberSchema::class,
			];
			foreach ($schemas as $schema) {
				$this->storage->create($schema);
			}
			self::assertTrue(true, 'everything is ok');
		}

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
			$this->storage->insert($this->entityManager->entity(LabelSchema::class, (object)['name' => true]));
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function testInsert() {
			$this->storage->insert($entity = $this->entityManager->entity(LabelSchema::class, $object = (object)[
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
			$this->storage->insert($this->entityManager->entity(LabelSchema::class, (object)[
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
			$this->storage->insert($this->entityManager->entity(LabelSchema::class, (object)[
				'name'   => 'unique',
				'system' => true,
			]));
			$this->storage->insert($this->entityManager->entity(LabelSchema::class, (object)[
				'name' => 'unique',
			]));
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testCollection() {
			$collection = $this->collectionManager->collection();
			$selectQuery = $collection->getSelectQuery();
			$selectQuery->use(LabelSchema::class);
			$entities = [];
			foreach ($collection as $record) {
				$entity = $record->getEntity(LabelSchema::class)->toObject();
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
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 * @throws FilterException
		 */
		public function testUpdate() {
			$this->storage->insert($entity = $this->entityManager->entity(ProjectSchema::class, (object)[
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
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testSave() {
			$this->storage->save($entity = $this->entityManager->entity(ProjectSchema::class, (object)[
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
			$this->expectException(StorageException::class);
			$this->expectExceptionMessage('Source schema [UserSchema] of entity differs from expected relation [ProjectMemberSchema] source schema [ProjectSchema]; did you swap source ($entity) and $target?');
			$project = $this->entityManager->entity(ProjectSchema::class, (object)[
				'name' => 'to be linked',
			]);
			$user = $this->entityManager->entity(UserSchema::class, (object)[
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
		 * @throws FilterException
		 * @throws SchemaException
		 * @throws StorageException
		 * @throws ValidatorException
		 */
		public function testAttach() {
			$project = $this->entityManager->entity(ProjectSchema::class, (object)[
				'uuid' => 'one',
				'name' => 'to be linked',
			]);
			$user = $this->entityManager->entity(UserSchema::class, (object)[
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
			$relation->set('owner', true);
			$this->storage->insert($relation);
			$relation = $this->storage->load(ProjectMemberSchema::class, $relation->get('uuid'));
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

		public function testLink() {
			$project = $this->entityManager->entity(ProjectSchema::class, (object)[
				'uuid' => 'two-pi',
				'name' => 'multilink, yaay',
			]);
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
			]);
		}
	}
