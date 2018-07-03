<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Crypt\CryptEngine;
	use Edde\Common\Filter\BoolFilter;
	use Edde\Common\Filter\GuidFilter;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Schema\SchemaProperty;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\Header;
	use Foo\Bar\Row;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class CrateTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;

		public function testLinks() {
			$headerSchema = $this->schemaManager->getSchema(Header::class);
			$rowSchema = $this->schemaManager->getSchema(Row::class);

			self::assertTrue($rowSchema->hasLink('header'));
			self::assertTrue($headerSchema->hasCollection('rowCollection'));

			/** @var $headerCrate ICrate */
			$headerCrate = $this->container->create(Header::class);
			self::assertInstanceOf(Header::class, $headerCrate);
			$headerCrate->setSchema($headerSchema);
		}

		public function testArraysException() {
			$this->expectException(CrateException::class);
			$this->expectExceptionMessage('Property [schema::hello] is not array; cannot add value.');
			$crate = new Crate();
			$schema = new Schema('schema');
			$crate->addProperty(new Property(new SchemaProperty($schema, 'hello')));
			$crate->add('hello', false);
		}

		public function testArrays() {
			$crate = new Crate();
			$schema = new Schema('schema');
			$crate->addProperty(new Property(new SchemaProperty($schema, 'hello', 'string', false, false, false, true)));
			$crate->add('hello', 'hello');
			$crate->add('hello', 'bello');
			$crate->add('hello', 'whepee!', 'key');
			self::assertEquals([
				0 => 'hello',
				1 => 'bello',
				'key' => 'whepee!',
			], $crate->get('hello'));
		}

		public function testGenerator() {
			$crate = new Crate();
			$schema = new Schema('schema');
			$crate->addProperty(new Property($property = new SchemaProperty($schema, 'guid', 'string', false, false, false, true)));
			$property->setGenerator($guidFilter = new GuidFilter());
			$guidFilter->lazyCryptEngine(new CryptEngine());
			self::assertNotEmpty($guid = $crate->get('guid'));
			self::assertEquals($guid, $crate->get('guid'));
		}

		public function testAutomagicallGenerator() {
			$crate = new Crate();
			$schema = new Schema('schema');
			$crate->addProperty(new Property($property = new SchemaProperty($schema, 'guid', 'string', false, false, false, true)));
			$property->setGenerator($guidFilter = new GuidFilter());
			$guidFilter->lazyCryptEngine(new CryptEngine());
			$dirty = [];
			$crate->update();
			foreach ($crate->getDirtyList() as $name => $property) {
				$dirty[$name] = $property->get();
			}
			self::assertArrayHasKey('guid', $dirty);
			self::assertSame($dirty['guid'], $crate->get('guid'));
		}

		public function testFilters() {
			$crate = new Crate();
			$schema = new Schema('schema');
			$crate->addProperty(new Property(new SchemaProperty($schema, 'guid', 'string', false, false, false, true)));
			$crate->addProperty(new Property($property = new SchemaProperty($schema, 'bool', 'bool', false, false, false, true)));
			$property->addFilter(new BoolFilter());
			self::assertFalse($crate->get('bool'));
			$crate->set('bool', 'on');
			self::assertTrue($crate->get('bool'));
		}

		public function testSetterGetterFilters() {
			$crate = new Crate();
			$schema = new Schema('schema');
			$crate->addProperty(new Property(new SchemaProperty($schema, 'guid', 'string', false, false, false, true)));
			$crate->addProperty(new Property($property = new SchemaProperty($schema, 'bool', 'bool', false, false, false, true)));
			$property->addSetterFilter(new BoolFilter());
			self::assertNull($crate->get('bool'));
			$crate->set('bool', 'on');
			self::assertTrue($crate->get('bool'));
		}

		public function testStaticCrate() {
			$this->expectException(CrateException::class);
			$this->expectExceptionMessage('Unknown value [foo] in crate [Edde\Common\Crate\Crate; anonymous].');
			$crate = new Crate();
			$crate->set('foo', false);
		}

		public function testDynamicCrate() {
			$crate = new Crate();
			$crate->dynamic([
				'500' => 'value of property named "500"',
				'600' => 200,
			]);
			self::assertFalse($crate->isDirty());
			self::assertEquals($crate->getProperty('500')
				->getSchemaProperty()
				->getType(), 'string');
			self::assertEquals($crate->getProperty('600')
				->getSchemaProperty()
				->getType(), 'integer');
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				Crate::class,
				Header::class,
				Row::class,
				Collection::class,
				ISchemaManager::class => SchemaManager::class,
				ISchemaFactory::class => SchemaFactory::class,
				ICrateFactory::class => CrateFactory::class,
				ICrateGenerator::class => DummyCrateGenerator::class,
			]);
			$this->crateFactory = $this->container->create(ICrateFactory::class);
			$this->schemaManager = $this->container->create(ISchemaManager::class);
			$headerSchema = new Schema(Header::class);
			$headerSchema->addPropertyList([
				$headerGuid = (new SchemaProperty($headerSchema, 'guid'))->unique()
					->required()
					->identifier(),
				new SchemaProperty($headerSchema, 'name'),
			]);
			$rowSchema = new Schema(Row::class);
			$rowSchema->addPropertyList([
				(new SchemaProperty($rowSchema, 'guid'))->unique()
					->identifier()
					->required(),
				$headerLink = (new SchemaProperty($rowSchema, 'header'))->required(),
				new SchemaProperty($rowSchema, 'name'),
				new SchemaProperty($rowSchema, 'value'),
			]);

			$rowSchema->linkTo('header', 'rowCollection', $headerLink, $headerGuid);
			$this->schemaManager->addSchema($headerSchema);
			$this->schemaManager->addSchema($rowSchema);
		}
	}
