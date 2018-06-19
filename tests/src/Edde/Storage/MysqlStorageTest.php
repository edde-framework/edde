<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\Container\ContainerException;
	use Edde\Container\Factory\InstanceFactory;
	use Edde\Schema\SchemaException;
	use Edde\Service\Container\Container;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Sql\CreateTableQuery;
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
		 * @throws ContainerException
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
			$this->container->inject($createTableQuery = new CreateTableQuery(MysqlStorage::TYPES));
			$createTableQuery->creates($schemas);
			self::assertTrue(true, 'everything is ok');
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
