<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Common\ContainerTest\MagicFactory;
	use Edde\Common\ContainerTest\TestMagicFactory;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once __DIR__ . '/../assets.php';

	class CallbackFactoryTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testCommon() {
			$factory = new CallbackFactory('name', $magicFactory = new MagicFactory(), false, false);
			$factory->setSingleton(false);
			self::assertFalse($factory->isSingleton());
			self::assertFalse($magicFactory->hasFlag());
			self::assertEquals('name', $factory->getName());
			self::assertInstanceOf(TestMagicFactory::class, $factory->create('name', [], $this->container));
			self::assertTrue($magicFactory->hasFlag());
		}

		public function testRecursiveFactory() {
			$this->expectException(FactoryException::class);
			$this->expectExceptionMessage('Factory [name] is locked; isn\'t there some circular dependency?');
			$factory = new CallbackFactory('name', function (CallbackFactory $f) {
				$f->create('name', [], $this->container);
			}, false, false);
			$factory->create('name', [$factory], $this->container);
		}

		protected function setUp() {
			$this->container = ContainerFactory::create();
		}
	}
