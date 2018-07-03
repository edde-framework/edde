<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Database\IDriver;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Crate\Crate;
	use Edde\Common\Crate\CrateFactory;
	use Edde\Common\Crate\DummyCrateGenerator;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Query\Schema\CreateSchemaQuery;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\JsonConverter;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use phpunit\framework\TestCase;

	class StorageTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var SqliteDriver
		 */
		protected $sqliteDriver;

		public function testSimpleStorable() {
			$crate = $this->crateFactory->crate(Crate::class, 'Foo\\Bar\\SimpleStorable', null);
			$schema = $this->schemaManager->getSchema('Foo\\Bar\\SimpleStorable');
			$this->storage->start();
			$this->storage->execute(new CreateSchemaQuery($schema));

			$crate->set('guid', $guid = sha1(random_bytes(64)));
			$crate->set('value', 'foobar');
			$this->storage->store($crate);

			$crate->set('guid', sha1(random_bytes(64)));
			$crate->set('value', 'barfoo');
			$this->storage->store($crate);

			$this->storage->commit();

			$query = new SelectQuery();
			$query->select()
				->all()
				->from()
				->source($schemaName = $schema->getSchemaName())
				->where()
				->eq()
				->property('guid')
				->parameter($guid);

			$crate = $this->storage->load(Crate::class, $query, $schemaName);
			self::assertEquals($guid, $crate->get('guid'));
			self::assertEquals('foobar', $crate->get('value'));
			$count = 0;
			foreach ($this->storage->collection(Crate::class, null, $schemaName) as $crate) {
				$count++;
			}
			self::assertEquals(2, $count);
		}

		public function testUpdate() {
			$this->storage->start();
			$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema('Group')));
			$this->storage->store($rootGroup = $this->crateFactory->crate(Crate::class, 'Group', null)
				->put([
					'guid' => sha1(random_bytes(64)),
					'name' => 'root',
				]));
			$selectQuery = new SelectQuery();
			$selectQuery->select()
				->all()
				->from()
				->source('Group')
				->where()
				->eq()
				->property('guid')
				->parameter($rootGroup->get('guid'));
			$load = $this->storage->load(Crate::class, $selectQuery, 'Group');
			self::assertEquals($rootGroup->get('guid'), $load->get('guid'));
			$rootGroup->set('name', 'The Epic Godness');
			$this->storage->store($rootGroup);
			$load = $this->storage->load(Crate::class, $selectQuery, 'Group');
			self::assertEquals($rootGroup->get('guid'), $load->get('guid'));
			self::assertEquals('The Epic Godness', $load->get('name'));
			$this->storage->commit();
		}

		public function testComplexStorable() {
			$this->storage->start();
			$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema('Group')));
			$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema('Identity')));
			$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema('IdentityGroup')));

			$this->storage->store($rootGroup = $this->crateFactory->crate(Crate::class, 'Group', null)
				->put([
					'guid' => sha1(random_bytes(64)),
					'name' => 'root',
				]))
				->store($guestGroup = $this->crateFactory->crate(Crate::class, 'Group', null)
					->put([
						'guid' => sha1(random_bytes(64)),
						'name' => 'guest',
					]))
				->store($godIdentity = $this->crateFactory->crate(Crate::class, 'Identity', null)
					->put([
						'guid' => sha1(random_bytes(64)),
						'name' => 'The God',
					]))
				->store($guestIdentity = $this->crateFactory->crate(Crate::class, 'Identity', null)
					->put([
						'guid' => sha1(random_bytes(64)),
						'name' => "The God's Guest",
					]))
				->store($rootGod = $this->crateFactory->crate(Crate::class, 'IdentityGroup', null)
					->put([
						'guid' => $identityGuid = sha1(random_bytes(64)),
					])
					->linkTo([
						'identity' => $godIdentity,
						'group' => $rootGroup,
					]))
				->store($guestGod = $this->crateFactory->crate(Crate::class, 'IdentityGroup', null)
					->put([
						'guid' => sha1(random_bytes(64)),
					])
					->linkTo([
						'identity' => $godIdentity,
						'group' => $guestGroup,
					]))
				->store($guestGuest = $this->crateFactory->crate(Crate::class, 'IdentityGroup', null)
					->put([
						'guid' => sha1(random_bytes(64)),
					])
					->linkTo([
						'identity' => $guestIdentity,
						'group' => $guestGroup,
					]));

			$groupList = [];
			foreach ($this->storage->collectionTo($godIdentity, 'IdentityGroup', 'identity', 'group', Crate::class) as $storable) {
				$groupList[] = $storable->get('name');
			}
			self::assertEquals([
				'root',
				'guest',
			], $groupList);

			$selectQuery = new SelectQuery();
			$selectQuery->select()
				->all()
				->from()
				->source('IdentityGroup')
				->where()
				->eq()
				->property('guid')
				->parameter($identityGuid);
			$identityGroup = $this->storage->load(Crate::class, $selectQuery, 'IdentityGroup');
			$linkIdentity = $identityGroup->getLink('identity');
			self::assertEquals($linkIdentity->array(), $godIdentity->array());
			$this->storage->commit();
		}

		public function testSourceException() {
			$this->expectException(UnknownSourceException::class);
			$this->expectExceptionMessage('SQLSTATE[HY000]: General error: 1 no such table: unknown source');
			$selectQuery = new SelectQuery();
			$selectQuery->select()
				->all()
				->from()
				->source('unknown source');
			$this->storage->execute($selectQuery);
		}

		public function testUniqueException() {
			$this->expectException(UniqueException::class);
			$this->expectExceptionMessage('SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed: Group.name');
			$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema('Group')));
			$this->storage->store($this->crateFactory->crate(Crate::class, 'Group', null)
				->put([
					'guid' => sha1(random_bytes(64)),
					'name' => 'root',
				]))
				->store($this->crateFactory->crate(Crate::class, 'Group', null)
					->put([
						'guid' => sha1(random_bytes(64)),
						'name' => 'root',
					]));
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				ISchemaFactory::class => SchemaFactory::class,
				ISchemaManager::class => SchemaManager::class,
				IStorage::class => DatabaseStorage::class,
				ITempDirectory::class => function () {
					return new TempDirectory(__DIR__ . '/temp');
				},
				IDriver::class => function (ITempDirectory $tempDirectory) {
					return $this->sqliteDriver = new SqliteDriver('sqlite:' . $tempDirectory->filename('storage.sqlite'));
				},
				ICrateFactory::class => CrateFactory::class,
				ICrateGenerator::class => DummyCrateGenerator::class,
			]);
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->create(JsonConverter::class));
			$schemaFactory = $container->create(ISchemaFactory::class);
			$schemaFactory->load(__DIR__ . '/assets/simple-storable.json');
			$schemaFactory->load(__DIR__ . '/assets/identity-storable.json');
			$schemaFactory->load(__DIR__ . '/assets/group-storable.json');
			$schemaFactory->load(__DIR__ . '/assets/identity-group-storable.json');
			$this->schemaManager = $container->create(ISchemaManager::class);
			$tempDirectory = $container->create(ITempDirectory::class);
			$tempDirectory->purge();
			$this->storage = $container->create(IStorage::class);
			$this->crateFactory = $container->create(ICrateFactory::class);
		}

		protected function tearDown() {
			$this->sqliteDriver->close();
			$tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$tempDirectory->purge();
		}
	}
