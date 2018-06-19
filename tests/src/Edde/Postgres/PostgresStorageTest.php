<?php
	declare(strict_types=1);
	namespace Edde\Postgres;

	use Edde\Container\ContainerException;
	use Edde\Container\Factory\InterfaceFactory;
	use Edde\Hydrator\SchemaHydrator;
	use Edde\Query\InsertQuery;
	use Edde\Schema\SchemaException;
	use Edde\Service\Hydrator\HydrateManager;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Security\RandomService;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\IStorage;
	use Edde\Storage\StorageException;
	use Edde\TestCase;
	use Edde\Transaction\TransactionException;
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

	class PostgresStorageTest extends TestCase {
		use Storage;
		use SchemaManager;
		use RandomService;
		use HydrateManager;

		/**
		 * @throws StorageException
		 */
		public function testPrepareDatabase() {
			$this->storage->exec('DROP SCHEMA IF EXISTS "test" CASCADE');
			$this->storage->exec('CREATE SCHEMA "test" AUTHORIZATION "edde"');
			$this->assertTrue(true, 'everything is OK!');
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
			$this->container->inject($createTableQuery = new CreateTableQuery());
			$createTableQuery->creates($schemas);
			self::assertTrue(true, 'everything is ok');
		}

		/**
		 * @throws ContainerException
		 * @throws StorageException
		 * @throws TransactionException
		 */
		public function testCollectionSimpleValue() {
			$this->container->inject($insertQuery = new InsertQuery($this->container->inject(new SchemaHydrator())));
			$insertQuery->inserts(ProjectSchema::class, [
				[
					'name' => 'project-01',
				],
				[
					'name' => 'project-02',
				],
			]);
			$record = null;
			foreach ($this->hydrateManager->value('SELECT COUNT(*) FROM project') as $record) {
				break;
			}
			self::assertEquals(2, $record);
		}

		/**
		 * @throws StorageException
		 */
		public function testCollection() {
			$actual = [];
			foreach ($this->hydrateManager->schema(ProjectSchema::class, 'SELECT * FROM project ORDER BY name') as $record) {
				unset($record['uuid'], $record['created']);
				$actual[] = $record;
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
					'status' => 0,
					'start'  => null,
					'end'    => null,
				],
			], $actual);
		}

		/**
		 * @inheritdoc
		 *
		 * @throws ContainerException
		 * @throws ReflectionException
		 * @throws SchemaException
		 * @throws StorageException
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
			$this->container->registerFactory(new InterfaceFactory(IStorage::class, PostgresStorage::class));
			$this->storage->exec('SET "search_path" TO "test"');
		}
	}
