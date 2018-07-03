<?php
	declare(strict_types = 1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Node\INode;
	use Edde\Common\Node\NodeQuery;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Converter\NodeConverter;
	use phpunit\framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	/**
	 * Converter manager related tests.
	 */
	class ConverterManagerTest extends TestCase {
		/**
		 * @var IConverterManager
		 */
		protected $converterManager;

		public function testUnknownConverter() {
			$this->expectException(ConverterException::class);
			$this->expectExceptionMessage('Cannot convert unknown source mime [unknown source] to [json].');
			$this->converterManager->convert('something here', 'unknown source', 'json');
		}

		public function testConflictException() {
			$this->expectException(ConverterException::class);
			$this->expectExceptionMessage('Converter [DummyConverter] has conflict with converter [DummyConverter] on mime [foo|bar].');
			$this->converterManager->registerConverter((new \DummyConverter())->register('foo', 'bar'));
			$this->converterManager->registerConverter((new \DummyConverter())->register('foo', 'bar'));
		}

		public function testDummyConverter() {
			self::assertEquals($expect = 'this will be null on output', $this->converterManager->convert($expect, 'boo', 'something'));
		}

		public function testObjectConverter() {
			$node = $this->converterManager->convert((object)[
				'foo' => [
					'bar' => (object)[
						'moo' => 'foo',
					],
				],
			], \stdClass::class, INode::class);
			self::assertInstanceOf(INode::class, $node);
			self::assertInstanceOf(INode::class, $first = NodeQuery::first($node, '//foo/bar'));
			self::assertEquals('foo', $first->getAttribute('moo'));
			self::assertTrue($first->isLeaf(), 'Selected node is not a leaf!');
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IConverterManager::class => ConverterManager::class,
			]);
			$this->converterManager = $container->create(IConverterManager::class);
			$this->converterManager->registerConverter((new \DummyConverter())->register('boo', 'something'));
			$this->converterManager->registerConverter(new NodeConverter());
		}
	}
