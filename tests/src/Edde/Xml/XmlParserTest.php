<?php
	declare(strict_types=1);
	namespace Edde\Xml;

	use Edde\Inject\Xml\XmlParserService;
	use Edde\TestCase;
	use TestXmlHandler;

	require_once(__DIR__ . '/assets/assets.php');

	class XmlParserTest extends TestCase {
		use XmlParserService;

		/**
		 * @throws XmlException
		 */
		public function testSimple() {
			$this->xmlParserService->file(__DIR__ . '/assets/simple.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlException
		 */
		public function testSimpleShort() {
			$this->xmlParserService->file(__DIR__ . '/assets/simple-short.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlException
		 */
		public function testSimpleAttribute() {
			$this->xmlParserService->file(__DIR__ . '/assets/simple-attribute.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[
						'foo'   => 'bar',
						'bar'   => 'foo',
						'class' => 'Some\Strange\Characters',
					],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlException
		 */
		public function testSimpleShortAttribute() {
			$this->xmlParserService->file(__DIR__ . '/assets/simple-short-attribute.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[
						'foo'   => 'bar',
						'bar'   => 'foo',
						'class' => 'Some\Strange\Characters',
					],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlException
		 */
		public function testBitLessSimple() {
			$this->xmlParserService->file(__DIR__ . '/assets/a-bit-less-simple.xml', $handler = new TestXmlHandler());
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
			], $handler->getTags());
		}

		/**
		 * @throws XmlException
		 */
		public function testComment() {
			$this->xmlParserService->file(__DIR__ . '/assets/comment-test.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'node',
					[],
				],
				[
					'poo',
					[],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlException
		 */
		public function testSimpleMultilineAttributes() {
			$this->xmlParserService->file(__DIR__ . '/assets/simple-multiline-attributes.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'f',
					[
						'name'         => 'foo',
						'device-class' => 'bar',
					],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlException
		 */
		public function testMultilineAttributes() {
			$this->xmlParserService->file(__DIR__ . '/assets/multiline-attributes.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
				[
					'foo',
					[
						'name'         => 'foo',
						'device-class' => 'bar',
					],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlException
		 */
		public function testXmlHeader() {
			$this->xmlParserService->file(__DIR__ . '/assets/xml-with-header.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'foo',
					[],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlException
		 */
		public function testNewlineBetweenNodes() {
			$this->xmlParserService->file(__DIR__ . '/assets/newline-between-nodes.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'r',
					[],
				],
				[
					'node',
					[
						'attr'              => 'ibute',
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
			], $handler->getTags());
		}

		/**
		 * @throws XmlException
		 */
		public function testStringParse() {
			$this->xmlParserService->string('<r>
	<node attr="ibute" another-attribute="foo"/>
	<another-node foo="bar" boo="poo"/>
</r>
', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'r',
					[],
				],
				[
					'node',
					[
						'attr'              => 'ibute',
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
			], $handler->getTags());
		}
	}
