<?php
	declare(strict_types = 1);

	namespace Edde\Common\Resource;

	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\Node\Node;
	use Edde\Common\Xml\XmlParser;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\JsonConverter;
	use Edde\Ext\Converter\PhpConverter;
	use Edde\Ext\Converter\XmlConverter;
	use phpunit\framework\TestCase;

	class ResourceManagerTest extends TestCase {
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;

		public function testJson() {
			self::assertInstanceOf(INode::class, $source = $this->resourceManager->file(__DIR__ . '/assets/sample.json'));
			self::assertEquals('foo', $source->getName());
			self::assertEquals('moo', $source->getValue());
			self::assertEquals([
				'foo' => 'foo',
				'poo' => 'poo',
				'bar' => 'bar',
			], $source->getAttributeList());
			self::assertEquals([
				'meta' => 'list',
			], $source->getMetaList());
		}

		public function testJsonWithRoot() {
			self::assertInstanceOf(INode::class, $source = $this->resourceManager->file(__DIR__ . '/assets/sample.json', null, $root = new Node('my-root')));
			self::assertFalse($root->isLeaf(), 'Root has not been filled, oops!');
			self::assertCount(1, $nodeList = $root->getNodeList());
			/** @var $source INode */
			$source = $nodeList[0];
			self::assertEquals('moo', $source->getName());
			self::assertNull($source->getValue());
			self::assertEquals([
				'foo' => 'bar',
			], $source->getAttributeList());
			self::assertEquals([
				'meta' => 'meta-moo',
			], $source->getMetaList());
		}

		public function testXml() {
			self::assertInstanceOf(INode::class, $source = $this->resourceManager->file(__DIR__ . '/assets/sample.xml'));
			self::assertEquals('foo', $source->getName());
			self::assertEquals('moo', $source->getValue());
			self::assertEquals([
				'foo' => 'foo',
				'poo' => 'poo',
				'bar' => 'bar',
			], $source->getAttributeList());
			self::assertEmpty($source->getMetaList());
		}

		public function testPhpInclude() {
			self::assertInstanceOf(INode::class, $source = $this->resourceManager->file(__DIR__ . '/assets/sample.php'));
			self::assertEquals('foo', $source->getName());
			self::assertEquals('moo', $source->getValue());
			self::assertEquals([
				'foo' => 'foo',
				'poo' => 'poo',
				'bar' => 'bar',
			], $source->getAttributeList());
			self::assertEquals([
				'meta' => 'list',
			], $source->getMetaList());
			/**
			 * because php resource handler is based on a require, it must be working more than once
			 */
			self::assertInstanceOf(INode::class, $source = $this->resourceManager->file(__DIR__ . '/assets/sample.php'));
			self::assertEquals('foo', $source->getName());
			self::assertEquals('moo', $source->getValue());
			self::assertEquals([
				'foo' => 'foo',
				'poo' => 'poo',
				'bar' => 'bar',
			], $source->getAttributeList());
			self::assertEquals([
				'meta' => 'list',
			], $source->getMetaList());
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				IXmlParser::class => XmlParser::class,
			]);
			$this->resourceManager = $container->create(IResourceManager::class);
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->create(JsonConverter::class));
			$converterManager->registerConverter($container->create(XmlConverter::class));
			$converterManager->registerConverter($container->create(PhpConverter::class));
		}
	}
