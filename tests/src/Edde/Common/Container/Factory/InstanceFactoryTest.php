<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\FactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once __DIR__ . '/../assets.php';

	/**
	 * InstanceFactory atomic test.
	 */
	class InstanceFactoryTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testCommon() {
			$factory = new InstanceFactory('name', $this);
			self::assertEquals('name', $factory->getName());
			self::assertEmpty($factory->getParameterList());
			self::assertSame($this, $factory->create('name', [], $container = $this->container));
			self::assertSame($this, $factory->create('name', [], $container));
			self::assertTrue($factory->isSingleton());
		}

		public function testFactoryException() {
			$this->expectException(FactoryException::class);
			$this->expectExceptionMessage('Something went wrong. God will kill one cute kitten and The Deep Evil of The Most Evilest Hell will eat it!');
			$factory = new InstanceFactory('name', $this);
			$factory->factory('name', [], $this->container);
		}

		public function testOnSetup() {
			$this->expectException(FactoryException::class);
			$this->expectExceptionMessage('Cannot register deffered handler on [Edde\Common\Container\Factory\InstanceFactory]; setup handlers are not supported by this factory.');
			$factory = new InstanceFactory('name', $this);
			$factory->deffered(function () {
			});
		}

		protected function setUp() {
			$this->container = ContainerFactory::create();
		}
	}
