<?php
	declare(strict_types=1);

	namespace Edde\Common\Node;

	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Node\INode;
	use Edde\Ext\Test\TestCase;

	class NodeUtilsTest extends TestCase {
		use LazyConverterManagerTrait;
		static protected $source = '
		{
		        "packet": {
		                "version": "1.1",
		                "id": "some-guid-of-this-packet-even-this-text-could-be-used!",
		                "elements": {
		                        "request": [
			                        {
			                                "id": "aa-bb-cc",
			                                "request": "//some.namespace/execute-this",
			                                "foo": "bar",
			                                "name-of-node": {
			                                        "foo": {}
			                                }
			                        },
			                        {
			                                "id": "aa-dd-cc",
			                                "request": "//some.namespace/another-request"
			                        }
		                        ],
		                        "event": {
		                                "id": "cc-bb-aa",
		                                "event": "//namespace.of.event/yapee"
		                        }
		                },
		                "references": {
	                                "error": {
		                                "id": "error-id-1",
		                                "code": -120,
		                                "message": "I like errors, hehehe!",
		                                "reference": "aa-bb-cc"
	                                }
	                        }
		        }
		}';

		protected function doTest(INode $root) {
			$node = $root;
			self::assertEquals('packet', $node->getName());
			self::assertEquals('1.1', $node->getAttribute('version'));
			self::assertEquals('some-guid-of-this-packet-even-this-text-could-be-used!', $node->getAttribute('id'));
			self::assertCount(2, $node->getNodeList());

			self::assertInstanceOf(INode::class, $node = NodeQuery::first($root, '/packet/elements/*'));
			self::assertEquals('request', $node->getName());
			self::assertEquals('aa-bb-cc', $node->getAttribute('id'));
			self::assertEquals('//some.namespace/execute-this', $node->getAttribute('request'));
			self::assertEquals('bar', $node->getAttribute('foo'));
			self::assertCount(1, $node->getNodeList());

			self::assertInstanceOf(INode::class, $node = NodeQuery::first($root, '/packet/elements/event'));
			self::assertEquals('event', $node->getName());
			self::assertEquals('cc-bb-aa', $node->getAttribute('id'));
			self::assertEquals('//namespace.of.event/yapee', $node->getAttribute('event'));
			self::assertCount(0, $node->getNodeList());

			self::assertInstanceOf(INode::class, $node = NodeQuery::first($root, '/packet/elements/request/name-of-node'));
			self::assertEquals('name-of-node', $node->getName());
			self::assertCount(1, $node->getNodeList());

			self::assertInstanceOf(INode::class, NodeQuery::first($root, '/packet/elements/request/name-of-node/foo'));
		}

		public function testSimpleConvert() {
			$this->doTest(NodeUtils::toNode($this->converterManager->convert(self::$source, 'application/json', ['object'])->convert()->getContent()));
		}

		public function testConverterManager() {
			$this->doTest($this->converterManager->convert(self::$source, 'application/json', [INode::class])->convert()->getContent());
		}

		public function testNodeConversion() {
			$node = $this->converterManager->convert(self::$source, 'application/json', [INode::class])->convert()->getContent();
			$object = NodeUtils::fromNode($node);
			self::assertEquals((object)[
				'packet' => (object)[
					'version'    => '1.1',
					'id'         => 'some-guid-of-this-packet-even-this-text-could-be-used!',
					'elements'   => (object)[
						'request' => [
							(object)[
								'id'           => 'aa-bb-cc',
								'request'      => '//some.namespace/execute-this',
								'foo'          => 'bar',
								'name-of-node' => (object)[
									'foo' => (object)[],
								],
							],
							(object)[
								'id'      => 'aa-dd-cc',
								'request' => '//some.namespace/another-request',
							],
						],
						'event'   => (object)[
							'id'    => 'cc-bb-aa',
							'event' => '//namespace.of.event/yapee',
						],
					],
					'references' => (object)[
						'error' => (object)[
							'id'        => 'error-id-1',
							'code'      => -120,
							'message'   => 'I like errors, hehehe!',
							'reference' => 'aa-bb-cc',
						],
					],
				],
			], $object);
		}

		/**
		 * node to object test
		 */
		public function testCompactConversionEquality() {
			self::assertEquals(json_decode(self::$source), NodeUtils::fromNode($this->converterManager->convert(self::$source, 'application/json', [INode::class])->convert()->getContent()));
		}

		/**
		 * json to json conversions test
		 */
		public function testCompactConverter() {
			self::assertEquals(json_decode(self::$source), $this->converterManager->convert($this->converterManager->convert(self::$source, 'application/json', [INode::class])->convert()->getContent(), INode::class, [\stdClass::class])->convert()->getContent());
		}
	}
