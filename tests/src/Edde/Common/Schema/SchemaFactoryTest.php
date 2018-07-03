<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Node\Node;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\JsonConverter;
	use Edde\Ext\Converter\PhpConverter;
	use phpunit\framework\TestCase;

	class SchemaFactoryTest extends TestCase {
		/**
		 * @var ISchemaFactory
		 */
		protected $schemaFactory;

		public function testCommon() {
			$this->schemaFactory->addSchemaNode($haderSchemaNode = (new Node('Header', null, ['namespace' => 'Foo\\Bar']))->addNodeList([
				(new Node('property-list'))->addNodeList([
					new Node('guid', null, [
						'unique' => true,
						'required' => true,
						'identifier' => true,
					]),
					new Node('name', null, [
						'required' => true,
					]),
				]),
				(new Node('collection'))->addNodeList([
					new Node('rowCollection', 'guid', [
						'schema' => 'Foo\\Bar\\Row',
						'property' => 'header',
					]),
				]),
			]));
			$this->schemaFactory->addSchemaNode($rowSchemaNode = (new Node('Row', null, ['namespace' => 'Foo\\Bar']))->addNodeList([
				(new Node('property-list'))->addNodeList([
					new Node('guid', null, [
						'unique' => true,
						'required' => true,
						'identifier' => true,
					]),
					new Node('header', null, [
						'required' => true,
					]),
					new Node('name', null, [
						'required' => true,
						'type' => 'string[]',
					]),
				]),
				(new Node('link'))->addNodeList([
					new Node('header', 'header', [
						'schema' => 'Foo\\Bar\\Header',
						'property' => 'guid',
					]),
				]),
			]));

			$schemaList = $this->schemaFactory->create();
			self::assertArrayHasKey('Foo\\Bar\\Header', $schemaList);
			self::assertArrayHasKey('Foo\\Bar\\Row', $schemaList);

			$headerSchema = $schemaList['Foo\\Bar\\Header'];
			self::assertTrue($headerSchema->hasCollection('rowCollection'));
			self::assertEquals([
				'guid',
				'name',
			], array_keys($headerSchema->getPropertyList()));

			$rowSchema = $schemaList['Foo\\Bar\\Row'];
			self::assertTrue($rowSchema->hasLink('header'));
			self::assertEquals([
				'guid',
				'header',
				'name',
			], array_keys($rowSchema->getPropertyList()));
			$nameProperty = $rowSchema->getProperty('name');
			self::assertTrue($nameProperty->isArray());
			self::assertEquals('string', $nameProperty->getType());
		}

		public function testResourceManager() {
			$this->schemaFactory->load(__DIR__ . '/assets/header-schema.json');
			$this->schemaFactory->load(__DIR__ . '/assets/row-schema.json');
			$this->schemaFactory->load(__DIR__ . '/assets/simple-crate.php');
			$schemaList = $this->schemaFactory->create();
			self::assertArrayHasKey('Foo\\Bar\\Header', $schemaList);
			self::assertArrayHasKey('Foo\\Bar\\Row', $schemaList);
			$headerSchema = $schemaList['Foo\\Bar\\Header'];
			self::assertTrue($headerSchema->hasCollection('rowCollection'));
			self::assertEquals([
				'guid',
				'name',
			], array_keys($headerSchema->getPropertyList()));

			$rowSchema = $schemaList['Foo\\Bar\\Row'];
			self::assertTrue($rowSchema->hasLink('header'));
			self::assertTrue($rowSchema->hasMeta('some'));
			self::assertEquals($rowSchema->getMeta('some'), 'meta-data');
			self::assertEquals([
				'guid',
				'header',
			], array_keys($rowSchema->getPropertyList()));
			$fooSchema = $schemaList['Foo'];
			self::assertEquals([
				'guid',
				'bool',
				'auto-bool',
			], array_keys($fooSchema->getPropertyList()));
			self::assertTrue($fooSchema->getProperty('guid')
				->hasGenerator());
			$boolProperty = $fooSchema->getProperty('bool');
			self::assertTrue($boolProperty->filter('1'));
			self::assertTrue($boolProperty->filter('on'));
			self::assertFalse($boolProperty->filter('0'));
			self::assertFalse($boolProperty->filter('off'));
			self::assertFalse($boolProperty->filter(null));

			$automaficallBoolProperty = $fooSchema->getProperty('auto-bool');
			self::assertTrue($automaficallBoolProperty->filter('1'));
			self::assertTrue($automaficallBoolProperty->filter('on'));
			self::assertFalse($automaficallBoolProperty->filter('0'));
			self::assertFalse($automaficallBoolProperty->filter('off'));
			self::assertFalse($automaficallBoolProperty->filter(null));
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				ISchemaFactory::class => SchemaFactory::class,
			]);
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->create(JsonConverter::class));
			$converterManager->registerConverter($container->create(PhpConverter::class));
			$this->schemaFactory = $container->create(ISchemaFactory::class);
		}
	}
