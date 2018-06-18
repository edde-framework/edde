<?php
	declare(strict_types=1);
	namespace Edde\Mysql;

	use Edde\Container\ContainerException;
	use Edde\Container\Factory\InstanceFactory;
	use Edde\Hydrator\SchemaHydrator;
	use Edde\Hydrator\ValueHydrator;
	use Edde\Schema\SchemaException;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\IStorage;
	use Edde\Storage\StorageException;
	use Edde\TestCase;
	use IssueProjectSchema;
	use IssueSchema;
	use LabelSchema;
	use OrganizationSchema;
	use ProjectLabelSchema;
	use ProjectMemberSchema;
	use ProjectOrganizationSchema;
	use ProjectSchema;
	use ReflectionException;
	use Throwable;
	use ToBeOrdered;
	use UserSchema;

	class MysqlStorageTest extends TestCase {
		use Container;
		use Storage;
		use SchemaManager;

		/**
		 * @throws StorageException
		 */
		public function testPrepareDatabase() {
			$this->storage->exec('DROP DATABASE `edde`');
			$this->storage->exec('CREATE DATABASE `edde`');
			$this->storage->exec('USE `edde`');
			$this->assertTrue(true, 'everything looks nice even here!');
		}

		/**
		 * @throws SchemaException
		 * @throws Throwable
		 */
		public function testCreateSchema() {
			$schemas = [
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
			];
			foreach ($schemas as $schema) {
				$this->container->inject($createTableQuery = new CreateTableQuery($this->schemaManager->getSchema($schema)));
				$createTableQuery->create($this->storage);
			}
			self::assertTrue(true, 'everything is ok');
		}

		/**
		 * @throws StorageException
		 */
		public function testCollectionSimpleValue() {
			foreach ($this->storage->hydrate('SELECT COUNT(*) FROM project', new ValueHydrator()) as $record) {
			}
		}

		/**
		 * @throws SchemaException
		 * @throws StorageException
		 */
		public function testCollection() {
			foreach ($this->storage->hydrate('SELECT * FROM project', new SchemaHydrator($this->schemaManager->getSchema(ProjectSchema::class))) as $record) {
			}
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
			$this->container->registerFactory(new InstanceFactory(IStorage::class, MysqlStorage::class));
		}
	}
