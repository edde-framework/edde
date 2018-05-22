<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use DateTime;
	use Edde\Container\ContainerException;
	use Edde\Schema\SchemaException;
	use Edde\Service\Collection\CollectionManager;
	use Edde\Service\Collection\EntityManager;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\TestCase;
	use Edde\Validator\ValidatorException;
	use LabelSchema;
	use ProjectSchema;
	use ReflectionException;
	use UserSchema;
	use VoidSchema;
	use function property_exists;

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
			$source = $this->storage->insert(LabelSchema::class, $object = (object)[
				'name' => 'this entity is new',
			]);
			self::assertNotEquals($object, $source);
			self::assertTrue(property_exists($source, 'uuid'));
			self::assertTrue(property_exists($source, 'system'));
			self::assertNotEmpty($source->uuid);
			/**
			 * because insert returns data actually inserted into database
			 */
			self::assertEquals(0, $source->system);
		}

		/**
		 * @throws StorageException
		 */
		public function testInsertException2() {
			$this->expectException(ValidatorException::class);
			$this->expectExceptionMessage('Required value [LabelSchema::name] is not set or null.');
			$this->storage->insert(LabelSchema::class, (object)[
				'name' => null,
			]);
		}

		/**
		 * @throws StorageException
		 */
		public function testInsertUnique() {
			$this->expectException(DuplicateEntryException::class);
			$this->storage->insert(LabelSchema::class, (object)[
				'name'   => 'unique',
				'system' => true,
			]);
			$this->storage->insert(LabelSchema::class, (object)[
				'name' => 'unique',
			]);
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
		 * @throws StorageException
		 */
		public function testUpdate() {
			$source = $this->storage->insert(ProjectSchema::class, $object = (object)[
				'name'    => 'some-project-here',
				'created' => new DateTime(),
				'start'   => new DateTime(),
				'end'     => new DateTime(),
			]);
			$object->end = null;
			$object->uuid = $source->uuid;
			$this->storage->update(ProjectSchema::class, $object);
			$actual = $this->storage->load(ProjectSchema::class, $source->uuid);
			self::assertNull($actual->end);
			self::assertInstanceOf(DateTime::class, $actual->start);
		}

		/**
		 * @throws SchemaException
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$this->schemaManager->loads([
				VoidSchema::class,
				LabelSchema::class,
				ProjectSchema::class,
				UserSchema::class,
			]);
		}
	}
