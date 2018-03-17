<?php
	declare(strict_types=1);
	namespace Edde\Xml;

	use Edde\Exception\Xml\XmlParserException;
	use Edde\Inject\Xml\XmlParser;
	use Edde\TestCase;
	use TestXmlHandler;

	require_once(__DIR__ . '/assets/assets.php');

	class XmlParserTest extends TestCase {
		use XmlParser;

		/**
		 * @throws XmlParserException
		 */
		public function testSimple() {
			$this->xmlParser->file(__DIR__ . '/assets/simple.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlParserException
		 */
		public function testSimpleShort() {
			$this->xmlParser->file(__DIR__ . '/assets/simple-short.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'root',
					[],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlParserException
		 */
		public function testSimpleAttribute() {
			$this->xmlParser->file(__DIR__ . '/assets/simple-attribute.xml', $handler = new TestXmlHandler());
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
		 * @throws XmlParserException
		 */
		public function testSimpleShortAttribute() {
			$this->xmlParser->file(__DIR__ . '/assets/simple-short-attribute.xml', $handler = new TestXmlHandler());
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
		 * @throws XmlParserException
		 */
		public function testBitLessSimple() {
			$this->xmlParser->file(__DIR__ . '/assets/a-bit-less-simple.xml', $handler = new TestXmlHandler());
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
		 * @throws XmlParserException
		 */
		public function testComment() {
			$this->xmlParser->file(__DIR__ . '/assets/comment-test.xml', $handler = new TestXmlHandler());
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
		 * @throws XmlParserException
		 */
		public function testSimpleMultilineAttributes() {
			$this->xmlParser->file(__DIR__ . '/assets/simple-multiline-attributes.xml', $handler = new TestXmlHandler());
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
		 * @throws XmlParserException
		 */
		public function testMultilineAttributes() {
			$this->xmlParser->file(__DIR__ . '/assets/multiline-attributes.xml', $handler = new TestXmlHandler());
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
		 * @throws XmlParserException
		 */
		public function testXmlHeader() {
			$this->xmlParser->file(__DIR__ . '/assets/xml-with-header.xml', $handler = new TestXmlHandler());
			self::assertEquals([
				[
					'foo',
					[],
				],
			], $handler->getTags());
		}

		/**
		 * @throws XmlParserException
		 */
		public function testNewlineBetweenNodes() {
			$this->xmlParser->file(__DIR__ . '/assets/newline-between-nodes.xml', $handler = new TestXmlHandler());
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
		 * @throws XmlParserException
		 */
		public function testStringParse() {
			$this->xmlParser->string('<r>
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
