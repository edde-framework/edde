<?php
	declare(strict_types = 1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class NodeTest extends TestCase {
		public function testQuery() {
			$queryList = [
				/**
				 * select node based on more attributes
				 */
				'/**/[footribute][bootribute]' => function (array $nodeList, $query) {
					self::assertCount(1, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					$node = reset($nodeList);
					self::assertEquals('big-poo', $node->getName());
				},
				/**
				 * skipping path waiting for a certain node (foo, small-poo)
				 */
				'/root/**/foobar/**/small-poo' => function (array $nodeList, $query) {
					self::assertCount(1, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					$node = reset($nodeList);
					self::assertEquals('small-poo', $node->getName());
				},
				/**
				 * skipping path waiting for a node with name and attribute
				 */
				'/root/**/big-poo[footribute]' => function (array $nodeList, $query) {
					self::assertCount(1, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					$node = reset($nodeList);
					self::assertEquals('big-poo', $node->getName());
					self::assertTrue(array_key_exists('footribute', $node->getAttributeList()));
				},
				/**
				 * skipping path and waiting for a node
				 */
				'/root/**/big-poo' => function (array $nodeList, $query) {
					self::assertCount(7, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					foreach ($nodeList as $node) {
						self::assertEquals('big-poo', $node->getName());
					}
				},
				/**
				 * skipping one level of a path
				 */
				'/root/*/catch-me' => function (array $nodeList, $query) {
					self::assertCount(3, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					foreach ($nodeList as $node) {
						self::assertEquals('catch-me', $node->getName());
					}
				},
				/**
				 * catching exact path level
				 */
				'/root/repetative-node/*' => function (array $nodeList, $query) {
					self::assertCount(4, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					foreach ($nodeList as $node) {
						self::assertEquals(2, $node->getLevel());
					}
				},
				/**
				 * catching everything under the given path
				 */
				'/root/repetative-node/**' => function (array $nodeList, $query) {
					self::assertCount(5, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					foreach ($nodeList as $node) {
						self::assertTrue($node->getLevel() >= 2);
					}
				},
				/**
				 * return only the given matching node (can be more than one)
				 */
				'/root/going-deeper' => function (array $nodeList, $query) {
					self::assertCount(1, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					$node = reset($nodeList);
					self::assertEquals('going-deeper', $node->getName());
				},
				/**
				 * return only the given matching node with attributes
				 */
				'/root/going-deeper[fobar]' => function (array $nodeList, $query) {
					self::assertCount(1, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					$node = reset($nodeList);
					self::assertEquals('going-deeper', $node->getName());
					self::assertContains('fobar', array_keys($node->getAttributeList()));
				},
				/**
				 * return all nodes under the given path
				 */
				'/root/selective-selectness/**' => function (array $nodeList, $query) {
					self::assertCount(8, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					foreach ($nodeList as $node) {
						self::assertTrue($node->getLevel() >= 1);
					}
				},
				/**
				 * combination of greedness
				 */
				'/root/greedy-test/*/match/**/catch' => function (array $nodeList, $query) {
					self::assertCount(5, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					$valueList = [
						44,
						55,
						99,
						101,
						202,
					];
					$testList = [];
					foreach ($nodeList as $node) {
						self::assertEquals('catch', $node->getName());
						$testList[] = $node->getValue();
					}
					sort($valueList);
					sort($testList);
					self::assertEquals($valueList, $testList, 'Missmatched node values (probably bad matches)');
				},
				/**
				 * another combination of greedness
				 */
				'/root/greedy-test/**/match/*/catch' => function (array $nodeList, $query) {
					self::assertCount(3, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					$valueList = [
						44,
						99,
						202,
					];
					$testList = [];
					foreach ($nodeList as $node) {
						self::assertEquals('catch', $node->getName());
						$testList[] = $node->getValue();
					}
					sort($valueList);
					sort($testList);
					self::assertEquals($valueList, $testList, 'Missmatched node values (probably bad matches)');
				},
				/**
				 * another combination of greedness with attribute
				 */
				'/root/greedy-test/**/match/*/catch[fruit]' => function (array $nodeList, $query) {
					self::assertCount(1, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					$valueList = [
						202,
					];
					$testList = [];
					foreach ($nodeList as $node) {
						self::assertEquals('catch', $node->getName());
						self::assertEquals('banana', $node->getAttribute('fruit'));
						$testList[] = $node->getValue();
					}
					sort($valueList);
					sort($testList);
					self::assertEquals($valueList, $testList, 'Missmatched node values (probably bad matches)');
				},
				/**
				 * optional path skip
				 */
				'/root/greedy-test/?*/xyz' => function (array $nodeList, $query) {
					self::assertCount(1, $nodeList, sprintf('Node count missmatch for (%s)', $query));
					$valueList = [
						'xyz',
					];
					$testList = [];
					foreach ($nodeList as $node) {
						$testList[] = $node->getName();
					}
					sort($valueList);
					sort($testList);
					self::assertEquals($valueList, $testList, 'Missmatched node values (probably bad matches)');
				},
			];
			$iterator = $this->createIterator();
			foreach ($queryList as $query => $callback) {
				$nodeQuery = $this->createNodeQuery($query);
				call_user_func($callback, iterator_to_array($nodeQuery->query($iterator)), $query);
			}
		}

		protected function createIterator() {
			return NodeIterator::recursive($this->createNodeTree());
		}

		protected function createNodeTree() {
			$node = Node::create('root');
			$node->addNodeList([
				Node::create('going-deeper', null, ['fobar' => 'ble'])
					->addNodeList([
						Node::create('some-internal-stuff'),
					]),
				Node::create('another-deep-node')
					->addNodeList([
						Node::create('even-deeper-node')
							->addNodeList([
								Node::create('TheEnd'),
							]),
					]),
				Node::create('repetative-node')
					->addNodeList([
						Node::create('foo'),
					]),
				Node::create('repetative-node')
					->addNodeList([
						Node::create('bar'),
					]),
				Node::create('repetative-node')
					->addNodeList([
						Node::create('foo-bar'),
						Node::create('bara-foo')
							->addNode(Node::create('intergalactic-poo')),
					]),
				Node::create('variable-1')
					->addNodeList([
						Node::create('catch-me', 1),
					]),
				Node::create('variable-2')
					->addNodeList([
						Node::create('catch-me', 2),
					]),
				Node::create('variable-3')
					->addNodeList([
						Node::create('catch-me', 3),
					]),
				Node::create('independend-deepness')
					->addNodeList([
						Node::create('level1')
							->addNodeList([
								Node::create('catch-me', 4),
								Node::create('big-poo', 44, ['footribute' => 'footribute of the 44']),
								Node::create('level2')
									->addNodeList([
										Node::create('level3')
											->addNodeList([
												Node::create('big-poo', 11),
											]),
									]),
								Node::create('big-poo', 22),
							]),
					]),
				Node::create('independend-deepness')
					->addNodeList([
						Node::create('big-poo', 33),
						Node::create('level1')
							->addNodeList([
								Node::create('big-poo', 55),
								Node::create('level2')
									->addNodeList([
										Node::create('level3')
											->addNodeList([
												Node::create('big-poo', 77, [
													'footribute' => 'hello there :)',
													'bootribute' => 'che',
												]),
											]),
									]),
								Node::create('big-poo', 88),
							]),
					]),
				Node::create('selective-selectness')
					->addNodeList([
						Node::create('small-poo', 11),
						Node::create('level1')
							->addNodeList([
								Node::create('small-poo', 22),
								Node::create('foobar')
									->addNodeList([
										Node::create('small-poo', 33),
										Node::create('level3')
											->addNodeList([
												Node::create('small-poo', 44),
											]),
									]),
								Node::create('small-poo', 88),
							]),
					]),
				Node::create('greedy-test')
					->addNodeList([
						Node::create('xyz')
							->addNodeList([
								Node::create('match')
									->addNodeList([
										Node::create('catch', 33),
										Node::create('level-of-something')
											->addNodeList([
												Node::create('catch', 44),
												Node::create('another-level-of-something')
													->addNodeList([
														Node::create('catch', 55)
															->addNodeList([
																Node::create('catch-inside-catch'),
															]),
													]),
											]),
									]),
								Node::create('match')
									->addNodeList([
										Node::create('catch', 88),
										Node::create('level-of-something')
											->addNodeList([
												Node::create('catch', 99),
												Node::create('catch', 202, ['fruit' => 'banana']),
												Node::create('another-level-of-something')
													->addNodeList([
														Node::create('catch', 101)
															->addNodeList([
																Node::create('catch-inside-catch'),
															]),
													]),
											]),
									]),
							]),
						Node::create('big-xyz'),
						Node::create('small-xyz'),
						Node::create('testy-xyz'),
					]),
			]);
			return $node;
		}

		protected function createNodeQuery($query) {
			return NodeQuery::create($query);
		}

		public function testName() {
			$node = new Node('foo');
			self::assertSame('foo', $node->getName());
			$node->setName('bar');
			self::assertSame('bar', $node->getName());
		}

		public function testGetPath() {
			$node = new Node('foo');
			$barNode = new Node('bar');
			$foobarNode = new Node('foobar');
			$barNode->addNode($foobarNode);
			$node->addNode($barNode);
			self::assertEquals('/foo', $node->getPath());
			self::assertEquals('/foo/bar', $barNode->getPath());
			self::assertEquals('/foo/bar/foobar', $foobarNode->getPath());
		}

		public function testGetPathMetatribute() {
			$node = new Node('foo');
			$node->setMeta('meta', false);
			$barNode = new Node('bar');
			$foobarNode = new Node('foobar');
			$foobarNode->setAttribute('attr', false);
			$foobarNode->setAttribute('another', false);
			$foobarNode->setMeta('meta', true);
			$barNode->addNode($foobarNode);
			$node->addNode($barNode);
			self::assertEquals('/foo', $node->getPath());
			self::assertEquals('/foo/bar', $barNode->getPath());
			self::assertEquals('/foo/bar/foobar', $foobarNode->getPath());
			self::assertEquals('/foo/bar/foobar[attr][another]', $foobarNode->getPath(true));
			self::assertEquals('/foo(meta)/bar/foobar[attr][another](meta)', $foobarNode->getPath(true, true));
		}

		public function testSetValue() {
			$node = new Node('name', 'value');
			self::assertEquals('value', $node->getValue());
			$node->setValue('foo');
			self::assertEquals('foo', $node->getValue());
		}

		public function testHasAttribute() {
			$node = new Node('node', 'value', [
				'attr' => false,
				'foo' => null,
			]);
			self::assertTrue($node->hasAttribute('attr'));
			self::assertTrue($node->hasAttribute('foo'));
			self::assertFalse($node->hasAttribute('bar'));
		}

		public function testAttributeList() {
			$node = new Node();
			self::assertEmpty($node->getAttributeList());
			$node->setAttributeList($attributeList = [
				'foo' => 'bar',
				'bar' => 'foo',
			]);
			self::assertSame($attributeList, $node->getAttributeList());
		}

		public function testHasMeta() {
			$node = new Node('node', 'value');
			$node->setMetaList([
				'meta' => false,
				'foo' => null,
			]);
			self::assertTrue($node->hasMeta('meta'));
			self::assertTrue($node->hasMeta('foo'));
			self::assertFalse($node->hasMeta('bar'));
		}

		public function testMetaList() {
			$node = new Node();
			self::assertEmpty($node->getMetaList());
			$node->setMetaList($metaList = [
				'foo' => 'bar',
				'bar' => 'foo',
			]);
			self::assertSame($metaList, $node->getMetaList());
			self::assertEquals('bar', $node->getMeta('foo'));
			self::assertTrue($node->getMeta('nothing', true));
		}

		public function testGetRoot() {
			$node = NodeQuery::first($root = $this->createNodeTree(), '/root/another-deep-node/even-deeper-node/TheEnd');
			self::assertSame($root, $node->getRoot());

			self::assertTrue($root->isRoot());
			self::assertFalse($root->isChild());

			self::assertFalse($node->isRoot());
			self::assertTrue($node->isChild());
		}

		public function testMoveNodeList() {
			$alpha = new Node();
			$alpha->addNodeList($nodeList = [
				new Node('A'),
				new Node('B'),
				new Node('C'),
			]);
			$beta = new Node();
			$beta->moveNodeList($alpha, true);
			self::assertTrue($alpha->isLeaf());
			self::assertFalse($beta->isLeaf());
			self::assertEquals(3, $beta->getNodeCount());
			self::assertSame($nodeList, $beta->getNodeList());
		}

		public function testRemoveUnknownNode() {
			$this->expectException(NodeException::class);
			$this->expectExceptionMessage('The given node is not in current node list.');
			$alpha = new Node();
			$alpha->removeNode(new Node());
		}

		public function testSetNodeList() {
			$node = new Node();
			$node->addNode(new Node());
			$node->setNodeList($nodeList = [
				new Node(),
				new Node(),
				new Node(),
			]);
			self::assertEquals(3, $node->getNodeCount());
			self::assertSame($nodeList, $node->getNodeList());
		}

		public function testClearNodeList() {
			$node = new Node();
			$node->setNodeList($nodeList = [
				new Node(),
				new Node(),
				new Node(),
			]);
			$node->clearNodeList();
			self::assertTrue($node->isLeaf());
			/** @var $node INode */
			foreach ($nodeList as $node) {
				self::assertNull($node->getParent());
			}
		}

		public function testGetAncestorList() {
			$alpha = new Node();
			$beta = new Node();
			$gama = new Node();
			$beta->setParent($alpha);
			$gama->setParent($beta);
			self::assertEmpty($alpha->getAncestorList());
			self::assertSame([
				$alpha,
			], $beta->getAncestorList());
			self::assertSame([
				$alpha,
				$beta,
			], $gama->getAncestorList());
		}

		public function testGetTreeHeight() {
			$node = new Node();
			self::assertEquals(0, $node->getTreeHeight());
			$node->addNode((new Node)->addNode((new Node)->addNode(new Node)));
			self::assertEquals(3, $node->getTreeHeight());
		}

		public function testGetTreeSize() {
			$node = new Node();
			self::assertEquals(1, $node->getTreeSize());
			$node->addNode((new Node)->addNodeList([
				(new Node)->addNode(new Node),
				(new Node)->addNode(new Node),
				(new Node)->addNode(new Node),
			]));
			self::assertEquals(8, $node->getTreeSize());
		}

		public function testAccept() {
			$this->expectException(NodeException::class);
			$this->expectExceptionMessage('Current node [Edde\Common\Node\AlphaNode] doesn\'t accept given node [Edde\Common\Node\BetaNode].');
			$alpha = new AlphaNode();
			$beta = new BetaNode();
			$alpha->addNode($beta);
		}

		public function testPushAccept() {
			$this->expectException(NodeException::class);
			$this->expectExceptionMessage('Current node [Edde\Common\Node\AlphaNode] doesn\'t accept given node [Edde\Common\Node\BetaNode].');
			$alpha = new AlphaNode();
			$beta = new BetaNode();
			$alpha->pushNode($beta);
		}

		public function testAcceptNodeList() {
			$this->expectException(NodeException::class);
			$this->expectExceptionMessage('Current node [Edde\Common\Node\AlphaNode] doesn\'t accept given node [Edde\Common\Node\BetaNode].');
			$alpha = new AlphaNode();
			$beta = new BetaNode();
			$alpha->addNodeList([$beta]);
		}

		public function testAcceptParent() {
			$this->expectException(NodeException::class);
			$this->expectExceptionMessage('Cannot set parent for [Edde\Common\Node\AlphaNode]: parent [Edde\Common\Node\BetaNode] doesn\'t accept this node.');
			$alpha = new AlphaNode();
			$beta = new BetaNode();
			$alpha->setParent($beta);
		}

		public function testNodeIterator() {
			$node = new Node();
			$i = 0;
			$node->addNodeList([
				new Node($i++),
				new Node($i++),
				new Node($i),
			]);
			/** @var $node INode */
			foreach (new NodeIterator($node) as $i => $node) {
				self::assertEquals($node->getName(), $i);
			}
		}

		public function testNodeQueryFirst() {
			$node = new Node('root');
			$node->addNodeList([
				$first = new Node('a'),
				new Node('a'),
				new Node('a'),
			]);
			self::assertSame($first, NodeQuery::first($node, '/root/a'));
		}

		public function testNodeQueryLast() {
			$node = new Node('root');
			$node->addNodeList([
				new Node('a'),
				new Node('a'),
				$lastNode = new Node('a'),
			]);
			self::assertSame($lastNode, NodeQuery::last($node, '/root/a'));
		}

		public function testNodeQueryIsEmpty() {
			$node = new Node('root');
			$node->addNodeList([
				new Node('a'),
				new Node('a'),
				$lastNode = new Node('a'),
			]);
			$nodeQuery = new NodeQuery('/root/abc');
			self::assertTrue($nodeQuery->isEmpty($node));
			$nodeQuery = new NodeQuery('/root/a');
			self::assertFalse($nodeQuery->isEmpty($node));
		}

		public function testSetAttribute() {
			$node = new Node();
			$node->setAttribute('attr', 1);
			self::assertEquals(1, $node->getAttribute('attr'));
			self::assertTrue($node->getAttribute('dummy', true));
		}

		public function testRecursiveIterator() {
			$node = new Node();
			$node->addNodeList([
				(new Node('A'))->addNodeList([
					new Node('A.1'),
					new Node('A.2'),
					new Node('A.3'),
				]),
				(new Node('B'))->addNodeList([
					new Node('B.1'),
					new Node('B.2'),
					new Node('B.3'),
				]),
			]);
			$i = 0;
			foreach (NodeIterator::recursive($node) as $n) {
				$i++;
			}
			self::assertEquals(8, $i);
			self::assertTrue($node->isRoot());
		}

		public function testRecursiveIteratorWithRoot() {
			$node = new Node();
			$node->addNodeList([
				(new Node('A'))->addNodeList([
					new Node('A.1'),
					new Node('A.2'),
					new Node('A.3'),
				]),
				(new Node('B'))->addNodeList([
					new Node('B.1'),
					new Node('B.2'),
					new Node('B.3'),
				]),
			]);
			$i = 0;
			foreach (NodeIterator::recursive($node, true) as $n) {
				$i++;
			}
			self::assertEquals(9, $i);
			self::assertTrue($node->isRoot());
		}

		public function testSimpleNodeIterator() {
			$node = new Node();
			$node->addNodeList([
				(new Node('A'))->addNodeList([
					new Node('A.1'),
					new Node('A.2'),
					new Node('A.3'),
				]),
				(new Node('B'))->addNodeList([
					new Node('B.1'),
					new Node('B.2'),
					new Node('B.3'),
				]),
			]);
			$i = 0;
			foreach (new NodeIterator($node) as $n) {
				$i++;
			}
			self::assertEquals(2, $i);
		}

		public function testMove() {
			$node = new Node();
			$alphaNode = new Node();
			$betaNode = new Node();
			$alphaNode->addNode($node);
			self::assertEquals(1, $alphaNode->getNodeCount());
			self::assertSame($node->getParent(), $alphaNode);
			self::assertEmpty($betaNode->getNodeList());
			$betaNode->addNode($node, true);
			self::assertEquals(1, $betaNode->getNodeCount());
			self::assertSame($node->getParent(), $betaNode);
			self::assertEmpty($alphaNode->getNodeList());
			$alphaNode->addNode($node, true);
			self::assertEquals(1, $alphaNode->getNodeCount());
			self::assertSame($node->getParent(), $alphaNode);
			self::assertEmpty($betaNode->getNodeList());
		}

		public function testAttributeNamespace() {
			$node = new Node(null, null, [
				'foo' => 'bar',
				'a:foo' => 'bar',
				'a:too' => 'oot',
				'b:foo' => 'foo',
			]);
			self::assertEquals([
				'foo' => 'bar',
				'too' => 'oot',
			], $node->getAttributeList('a'));
			self::assertEquals([
				'foo' => 'foo',
			], $node->getAttributeList('b'));
			self::assertEquals([
				'foo' => 'bar',
				'a:foo' => 'bar',
				'a:too' => 'oot',
				'b:foo' => 'foo',
			], $node->getAttributeList());
			self::assertTrue($node->hasAttributeList('a'));
			self::assertTrue($node->hasAttributeList('b'));
			self::assertFalse($node->hasAttributeList('poo'));
		}

		public function testSwitch() {
			$foo = new Node('foo');
			$bar = new Node('bar');
			$poo = new Node('poo');

			$foo->addNode($bar->addNode($poo));
			self::assertEquals('/foo/bar/poo', $poo->getPath());
			self::assertSame($foo, $bar->getParent());
			self::assertSame($bar, $poo->getParent());
			$bar->switch($poo);
			self::assertEquals('/foo/poo', $poo->getPath());
			self::assertEquals('/foo/poo/bar', $bar->getPath());
			self::assertSame($foo, $poo->getParent());
			self::assertSame($poo, $bar->getParent());

			$foo->switch($bar);
			self::assertTrue($bar->isRoot());
			self::assertFalse($foo->isRoot());
			self::assertEquals('/bar/foo/poo', $poo->getPath());
		}
	}
