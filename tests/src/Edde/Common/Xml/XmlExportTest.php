<?php
	declare(strict_types=1);
	namespace Edde\Common\Xml;

	use Edde\Common\File\File;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\TreeIterator;
	use Edde\Exception\Node\NodeException;
	use Edde\Inject\Xml\XmlExport;
	use Edde\TestCase;

	class XmlExportTest extends TestCase {
		use XmlExport;

		/**
		 * @throws NodeException
		 */
		public function testSimple() {
			$this->xmlExport->export(TreeIterator::recursive(new Node('root'), true), $file = File::create(__DIR__ . '/temp/export.xml'));
			self::assertSame('<root/>
', $file->get());
		}

		/**
		 * @throws NodeException
		 */
		public function testSimpleAttribute() {
			$this->xmlExport->export(TreeIterator::recursive(new Node('root', [
				'foo' => 'bar',
				'bar' => 'foo',
			], null), true), $file = File::create(__DIR__ . '/temp/export.xml'));
			self::assertSame('<root foo="bar" bar="foo"/>
', $file->get());
		}

		/**
		 * @throws NodeException
		 */
		public function testSimpleAttributeEscape() {
			$this->xmlExport->export(TreeIterator::recursive(new Node('root', [
				'foo' => 'bar',
				'bar' => 'fo"o',
			], null), true), $file = File::create(__DIR__ . '/temp/export.xml'));
			self::assertSame('<root foo="bar" bar="fo&quot;o"/>
', $file->get());
		}

		/**
		 * @throws NodeException
		 */
		public function testSmallNode() {
			$node = new Node('root', [
				'foo' => 'bar',
				'bar' => 'fo"o',
			]);
			$node->add(new Node('foo'));
			$node->add($bar = new Node('bar', [], 'this-will-be-ignored'));
			$bar->add(new Node('node-inside-node', ['moo' => 'hello']));
			$bar->add($hidden = new Node('node-inside-node'));
			$hidden->add(new Node('a-little-secret-here', [], 'whoaaaaa'));
			$bar->add(new Node('node-inside-node-node'));
			$this->xmlExport->export(TreeIterator::recursive($node, true), $file = File::create(__DIR__ . '/temp/export.xml'));
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

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			@mkdir(__DIR__ . '/temp');
		}
	}
