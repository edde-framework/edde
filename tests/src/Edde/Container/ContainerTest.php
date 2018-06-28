<?php
	declare(strict_types=1);
	namespace Edde\Container;

	use Edde\EddeException;
	use Edde\Factory\ExceptionFactory;
	use Edde\Factory\InstanceFactory;
	use Edde\Factory\InterfaceFactory;
	use Edde\Factory\LinkFactory;
	use Edde\Service\Container\Container;
	use Edde\Test\AutowireDependencyObject;
	use Edde\Test\BarObject;
	use Edde\Test\ConstructorDependencyObject;
	use Edde\Test\FooObject;
	use Edde\Test\InjectDependencyObject;
	use Edde\TestCase;
	use ReflectionException;

	class ContainerTest extends TestCase {
		use Container;

		public function testCanHandle() {
			self::assertFalse($this->container->canHandle('nope'));
			self::assertTrue($this->container->canHandle(FooObject::class));
			self::assertTrue($this->container->canHandle(IContainer::class));
		}

		public function testInvalidFactory() {
			$this->expectException(ContainerException::class);
			$this->expectExceptionMessage('Unsupported factory definition [integer; prd].');
			ContainerFactory::createFactories([
				'prd',
			]);
		}

		public function testDropContainer() {
			$this->expectException(ContainerException::class);
			$this->expectExceptionMessage('No Container is available; please use some factory method of [Edde\Container\ContainerFactory] to create a container.');
			ContainerFactory::dropContainer();
			ContainerFactory::getContainer();
		}

		/**
		 * @throws ContainerException
		 */
		public function testContainerFactoryInstance() {
			self::assertSame($this->container, ContainerFactory::getContainer());
		}

		/**
		 * @throws ContainerException
		 */
		public function testUnknownFactory() {
			$this->expectException(ContainerException::class);
			$this->expectExceptionMessage('Unknown factory [unknown] for dependency [source].');
			$this->container->create('unknown', [], 'source');
		}

		/**'
		 * @throws ContainerException
		 */
		public function testConstructorDependency() {
			/** @var $constructorDependencyObject ConstructorDependencyObject */
			$constructorDependencyObject = $this->container->create(ConstructorDependencyObject::class);
			self::assertNotEmpty($constructorDependencyObject->fooObject);
			self::assertNotEmpty($constructorDependencyObject->barObject);
			self::assertInstanceOf(FooObject::class, $constructorDependencyObject->fooObject);
			self::assertInstanceOf(BarObject::class, $constructorDependencyObject->barObject);
			self::assertInstanceOf(FooObject::class, $constructorDependencyObject->barObject->fooObject);
			self::assertNotSame($constructorDependencyObject->fooObject, $constructorDependencyObject->barObject->fooObject);
		}

		/**
		 * @throws ContainerException
		 */
		public function testInjectDependency() {
			/** @var $injectDependencyObject InjectDependencyObject */
			$injectDependencyObject = $this->container->create(InjectDependencyObject::class);
			self::assertNotEmpty($injectDependencyObject->fooObject);
			self::assertNotEmpty($injectDependencyObject->barObject);
			self::assertInstanceOf(FooObject::class, $injectDependencyObject->fooObject);
			self::assertInstanceOf(BarObject::class, $injectDependencyObject->barObject);
			self::assertInstanceOf(FooObject::class, $injectDependencyObject->barObject->fooObject);
			self::assertNotSame($injectDependencyObject->fooObject, $injectDependencyObject->barObject->fooObject);
		}

		/**
		 * @throws ContainerException
		 */
		public function testLazyDependency() {
			/** @var $autowireDependencyObject AutowireDependencyObject */
			$autowireDependencyObject = $this->container->create(AutowireDependencyObject::class);
			self::assertFalse(isset($autowireDependencyObject->fooObject));
			self::assertFalse(isset($autowireDependencyObject->barObject));
			self::assertInstanceOf(FooObject::class, $autowireDependencyObject->fooObject);
			self::assertInstanceOf(BarObject::class, $autowireDependencyObject->barObject);
			self::assertInstanceOf(FooObject::class, $autowireDependencyObject->barObject->fooObject);
			self::assertNotSame($autowireDependencyObject->fooObject, $autowireDependencyObject->barObject->fooObject);
		}

		/**
		 * @throws ContainerException
		 */
		public function testInstanceFactory() {
			self::assertInstanceOf(FooObject::class, $fooObject = $this->container->create('instance'));
			self::assertSame($fooObject, $this->container->create('instance'));
		}

		/**
		 * @throws ContainerException
		 */
		public function testInstancedFactory() {
			self::assertInstanceOf(FooObject::class, $fooObject = $this->container->create('instanced'));
			self::assertSame($fooObject, $this->container->create('instanced'));
			/**
			 * manual test is necessary because container is optimized for redundant create dependency calls
			 */
			$dependency = $this->container->getFactory('instanced')->getReflection($this->container, 'instanced');
			self::assertEmpty($dependency->getInjects());
			self::assertEmpty($dependency->getParams());
			self::assertEmpty($dependency->getConfigurators());
		}

		/**
		 * @throws ContainerException
		 */
		public function testInterfaceDependencyFactory() {
			$dependency = $this->container->getFactory(IContainer::class)->getReflection($this->container, IContainer::class);
			self::assertEmpty($dependency->getInjects());
			self::assertEmpty($dependency->getParams());
			self::assertEmpty($dependency->getConfigurators());
		}

		/**
		 * @throws ContainerException
		 */
		public function testLinkFactory() {
			self::assertSame($this->container->create('instance'), $this->container->create('get-moo'));
		}

		/**
		 * @throws ContainerException
		 */
		public function testExceptionFactory() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('kaboom');
			$this->container->create('boom');
		}

		/**
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new InterfaceFactory('instance', FooObject::class));
			$this->container->registerFactory(new InterfaceFactory('instanced', FooObject::class));
			$this->container->registerFactory(new LinkFactory('get-moo', 'instance'));
			$this->container->registerFactory(new ExceptionFactory('boom', EddeException::class, 'kaboom'));
		}
	}
