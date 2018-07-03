<?php
	declare(strict_types = 1);

	namespace Edde\Common\Xml;

	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Common\Converter\ConverterManager;
	use Edde\Common\File\File;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\XmlConverter;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class XmlParserTest extends TestCase {
		/**
		 * @var IXmlParser
		 */
		protected $xmlParser;

		public function testSimple() {
			$this->xmlParser->file(__DIR__ . '/assets/simple.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
			], $handler->getTagList());
		}

		public function testSimpleShort() {
			$this->xmlParser->file(__DIR__ . '/assets/simple-short.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
			], $handler->getTagList());
		}

		public function testSimpleAttribute() {
			$this->xmlParser->file(__DIR__ . '/assets/simple-attribute.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[
						'foo' => 'bar',
						'bar' => 'foo',
						'class' => 'Some\Strange\Characters',
					],
				],
			], $handler->getTagList());
		}

		public function testSimpleShortAttribute() {
			$this->xmlParser->file(__DIR__ . '/assets/simple-short-attribute.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[
						'foo' => 'bar',
						'bar' => 'foo',
						'class' => 'Some\Strange\Characters',
					],
				],
			], $handler->getTagList());
		}

		public function testBitLessSimple() {
			$this->xmlParser->file(__DIR__ . '/assets/a-bit-less-simple.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[
						'r' => 'oot',
					],
				],
				[
					'item',
					[],
				],
				[
					'item2',
					['koo' => 'poo'],
				],
				[
					'internal',
					[],
				],
				[
					'hidden-tag',
					[],
				],
				[
					'tag-with-value',
					[],
				],
			], $handler->getTagList());
		}

		public function testComment() {
			$this->xmlParser->file(__DIR__ . '/assets/comment-test.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'node',
					[],
				],
				[
					'poo',
					[],
				],
			], $handler->getTagList());
		}

		public function testSimpleMultilineAttributes() {
			$this->xmlParser->file(__DIR__ . '/assets/simple-multiline-attributes.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'f',
					[
						'name' => 'foo',
						'device-class' => 'bar',
					],
				],
			], $handler->getTagList());
		}

		public function testMultilineAttributes() {
			$this->xmlParser->file(__DIR__ . '/assets/multiline-attributes.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
				[
					'foo',
					[
						'name' => 'foo',
						'device-class' => 'bar',
					],
				],
			], $handler->getTagList());
		}

		public function testXmlHeader() {
			$this->xmlParser->file(__DIR__ . '/assets/xml-with-header.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'foo',
					[],
				],
			], $handler->getTagList());
		}

		public function testNewlineBetweenNodes() {
			$this->xmlParser->file(__DIR__ . '/assets/newline-between-nodes.xml', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'r',
					[],
				],
				[
					'node',
					[
						'attr' => 'ibute',
						'another-attribute' => 'foo',
					],
				],
				[
					'another-node',
					[
						'foo' => 'bar',
						'boo' => 'poo',
					],
				],
			], $handler->getTagList());
		}

		public function testStringParse() {
			$this->xmlParser->string('<r>
	<node attr="ibute" another-attribute="foo"/>

	<another-node foo="bar" boo="poo"/>
</r>
', $handler = new \TestXmlHandler());
			self::assertEquals([
				[
					'r',
					[],
				],
				[
					'node',
					[
						'attr' => 'ibute',
						'another-attribute' => 'foo',
					],
				],
				[
					'another-node',
					[
						'foo' => 'bar',
						'boo' => 'poo',
					],
				],
			], $handler->getTagList());
		}

		public function testMimeType() {
			$file = new File(__DIR__ . '/assets/simple.xml');
			self::assertEquals('text/xml', $file->getMime());
		}

		public function testParserNode() {
			$container = ContainerFactory::create([
				IResourceManager::class => ResourceManager::class,
				IConverterManager::class => ConverterManager::class,
				IXmlParser::class => XmlParser::class,
			]);
			$resourceManager = $container->create(IResourceManager::class);
			$converterManager = $container->create(IConverterManager::class);
			$converterManager->registerConverter($container->create(XmlConverter::class));
			$node = $resourceManager->file(__DIR__ . '/assets/a-bit-less-simple.xml');
			self::assertEquals('root', $node->getName());
			self::assertEquals(['r' => 'oot'], $node->getAttributeList());
			self::assertCount(3, $node->getNodeList());
			$nodeIterator = new \ArrayIterator($node->getNodeList());
			$nodeIterator->rewind();
			self::assertEquals('item', $nodeIterator->current()
				->getName());
			$nodeIterator->next();
			self::assertEquals('item2', $nodeIterator->current()
				->getName());
			$nodeIterator->next();
			self::assertEquals('internal', $nodeIterator->current()
				->getName());
		}

		protected function setUp() {
			$this->xmlParser = new XmlParser();
		}
	}
