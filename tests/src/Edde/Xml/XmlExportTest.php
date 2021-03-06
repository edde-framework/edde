<?php
	declare(strict_types=1);
	namespace Edde\Xml;

	use Edde\File\File;
	use Edde\Node\Node;
	use Edde\Node\TreeIterator;
	use Edde\Service\Xml\XmlExportService;
	use Edde\TestCase;

	class XmlExportTest extends TestCase {
		use XmlExportService;

		public function testSimple() {
			$this->xmlExportService->export(TreeIterator::recursive(new Node('root'), true), $file = new File(__DIR__ . '/temp/export.xml'));
			self::assertSame('<root/>
', $file->load());
		}

		public function testSimpleAttribute() {
			$this->xmlExportService->export(TreeIterator::recursive(new Node('root', [
				'foo' => 'bar',
				'bar' => 'foo',
			], null), true), $file = new File(__DIR__ . '/temp/export.xml'));
			self::assertSame('<root foo="bar" bar="foo"/>
', $file->load());
		}

		public function testSimpleAttributeEscape() {
			$this->xmlExportService->export(TreeIterator::recursive(new Node('root', [
				'foo' => 'bar',
				'bar' => 'fo"o',
			], null), true), $file = new File(__DIR__ . '/temp/export.xml'));
			self::assertSame('<root foo="bar" bar="fo&quot;o"/>
', $file->load());
		}

		public function testSmallNode() {
			$node = new Node('root', [
				'foo' => 'bar',
				'bar' => 'fo"o',
			]);
			$node->add(new Node('foo'));
			$node->add($bar = new Node('bar', null, 'this-will-be-ignored'));
			$bar->add(new Node('node-inside-node', ['moo' => 'hello']));
			$bar->add($hidden = new Node('node-inside-node'));
			$hidden->add(new Node('a-little-secret-here', null, 'whoaaaaa'));
			$bar->add(new Node('node-inside-node-node'));
			$this->xmlExportService->export(TreeIterator::recursive($node, true), $file = new File(__DIR__ . '/temp/export.xml'));
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
', $file->load());
		}

		/** @inheritdoc */
		protected function setUp() {
			parent::setUp();
			@mkdir(__DIR__ . '/temp');
		}
	}
