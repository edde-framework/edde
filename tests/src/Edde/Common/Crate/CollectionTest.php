<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Schema\SchemaProperty;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\FooBarBar;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class CollectionTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;

		public function testCommon() {
			$collection = new Collection($this->crateFactory, 'Foo\\Bar\\FooBar', Crate::class);
			self::assertEmpty(iterator_to_array($collection));
			$crate = $collection->createCrate();
			self::assertEmpty(iterator_to_array($collection));
			$collection->addCrate($crate);
			self::assertNotEmpty($collectionArray = iterator_to_array($collection));
			self::assertCount(1, $collectionArray);
			$crate->set('guid', '12345');
			self::assertEquals('12345', $crate->get('guid'));
			self::assertInstanceOf(Crate::class, $crate);
		}

		public function testInstanceCrate() {
			$collection = new Collection($this->crateFactory, 'Foo\\Bar\\FooBarBar', FooBarBar::class);
			$crate = $collection->createCrate();
			self::assertInstanceOf(FooBarBar::class, $crate);
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				Crate::class,
				FooBarBar::class,
				ICrateFactory::class => CrateFactory::class,
				ICrateGenerator::class => DummyCrateGenerator::class,
				ISchemaManager::class => SchemaManager::class,
				ISchemaFactory::class => SchemaFactory::class,
			]);
			$this->crateFactory = $this->container->create(ICrateFactory::class);
			$schemaManager = $this->container->create(ISchemaManager::class);
			$schema = new Schema('Foo\\Bar\\FooBar');
			$schema->addProperty(new SchemaProperty($schema, 'guid'));
			$schema->addProperty(new SchemaProperty($schema, 'name'));
			$schema->addProperty(new SchemaProperty($schema, 'long-name'));
			$schemaManager->addSchema($schema);
			$schema = new Schema('Foo\\Bar\\FooBarBar');
			$schema->addProperty(new SchemaProperty($schema, 'guid'));
			$schema->addProperty(new SchemaProperty($schema, 'name'));
			$schema->addProperty(new SchemaProperty($schema, 'long-name'));
			$schemaManager->addSchema($schema);
		}
	}
