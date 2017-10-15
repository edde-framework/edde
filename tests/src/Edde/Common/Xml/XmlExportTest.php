<?php
	declare(strict_types=1);
	namespace Edde\Common\Xml;

		use Edde\Api\Node\NodeException;
		use Edde\Api\Xml\Inject\XmlExport;
		use Edde\Common\File\File;
		use Edde\Common\Node\Node;
		use Edde\Common\Node\NodeIterator;
		use Edde\Ext\Test\TestCase;

		class XmlExportTest extends TestCase {
			use XmlExport;

			/**
			 * @throws NodeException
			 */
			public function testSimple() {
				$this->xmlExport->export(NodeIterator::recursive(new Node('root'), true), $file = new File(__DIR__ . '/temp/export.xml'));
				self::assertSame('<root/>
', $file->get());
			}

			/**
			 * @throws NodeException
			 */
			public function testSimpleAttribute() {
				$this->xmlExport->export(NodeIterator::recursive(new Node('root', null, [
					'foo' => 'bar',
					'bar' => 'foo',
				]), true), $file = new File(__DIR__ . '/temp/export.xml'));
				self::assertSame('<root foo="bar" bar="foo"/>
', $file->get());
			}

			/**
			 * @throws NodeException
			 */
			public function testSimpleAttributeEscape() {
				$this->xmlExport->export(NodeIterator::recursive(new Node('root', null, [
					'foo' => 'bar',
					'bar' => 'fo"o',
				]), true), $file = new File(__DIR__ . '/temp/export.xml'));
				self::assertSame('<root foo="bar" bar="fo&quot;o"/>
', $file->get());
			}

			/**
			 * @throws NodeException
			 */
			public function testSmallNode() {
				$node = new Node('root', null, [
					'foo' => 'bar',
					'bar' => 'fo"o',
				]);
				$node->addNode(new Node('foo'));
				$node->addNode($bar = new Node('bar', 'this-will-be-ignored'));
				$bar->addNode(new Node('node-inside-node', null, ['moo' => 'hello']));
				$bar->addNode($hidden = new Node('node-inside-node'));
				$hidden->addNode(new Node('a-little-secret-here', 'whoaaaaa'));
				$bar->addNode(new Node('node-inside-node-node'));
				$this->xmlExport->export(NodeIterator::recursive($node, true), $file = new File(__DIR__ . '/temp/export.xml'));
				self::assertSame('<root foo="bar" bar="fo&quot;o">
	<foo/>
	<bar>
		<node-inside-node moo="hello"/>
		<node-inside-node>
			<a-little-secret-here>whoaaaaa</a-little-secret-here>
		</node-inside-node>
		<node-inside-node-node/>
	</bar>
</root>
', $file->get());
			}
		}
