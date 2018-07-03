<?php
	declare(strict_types=1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\Exception\UnknownFactoryException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\EddeException;
	use Edde\Common\Container\Factory\CallbackFactory;
	use Edde\Common\Container\Factory\ExceptionFactory;
	use Edde\Common\Container\Factory\InstanceFactory;
	use Edde\Common\Container\Factory\LinkFactory;
	use Edde\Common\Container\Factory\ProxyFactory;
	use Edde\Ext\Test\TestCase;
	use Edde\Test\AutowireDependencyObject;
	use Edde\Test\BarObject;
	use Edde\Test\ConstructorDependencyObject;
	use Edde\Test\FooObject;
	use Edde\Test\InjectDependencyObject;

	require_once __DIR__ . '/../assets/assets.php';

	class ContainerTest extends TestCase {
		use \Edde\Api\Container\Inject\Container;

		public function testUnknownFactory() {
			$this->expectException(UnknownFactoryException::class);
			$this->expectExceptionMessage('Unknown factory [unknown] for dependency [source].');
			$this->container->create('unknown', [], 'source');
		}

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

		public function testScalar() {
			self::assertSame(3.14, $this->container->create('scalar'));
		}

		public function testScalarDependency() {
			self::assertSame('pi=3.14', $this->container->create('scalar-dependency'));
		}

		public function testScalarParameter() {
			self::assertSame('3.14+foo', $this->container->create('scalar-parameter', ['foo']));
		}

		public function testAutomaticType() {
			self::assertSame('3.14', $this->container->create('string'));
		}

		public function testInstanceFactory() {
			self::assertInstanceOf(FooObject::class, $fooObject = $this->container->create('instance'));
			self::assertSame($fooObject, $this->container->create('instance'));
		}

		public function testInstanceCloneFactory() {
			self::assertInstanceOf(FooObject::class, $fooObject = $this->container->create('instance-clone'));
			self::assertNotSame($fooObject, $this->container->create('instance-clone'));
		}

		public function testInstancedFactory() {
			self::assertInstanceOf(FooObject::class, $fooObject = $this->container->create('instanced'));
			self::assertSame($fooObject, $this->container->create('instanced'));
			/**
			 * manual test is necessary because container is optimized for redundant create dependency calls
			 */
			$dependency = $this->container->getFactory('instanced')->createDependency($this->container);
			self::assertEmpty($dependency->getLazyList());
			self::assertEmpty($dependency->getParameterList());
			self::assertEmpty($dependency->getConfiguratorList());
			self::assertEmpty($dependency->getInjectList());
		}

		public function testInterfaceDependencyFactory() {
			$dependency = $this->container->getFactory(IContainer::class)->createDependency($this->container);
			self::assertEmpty($dependency->getLazyList());
			self::assertEmpty($dependency->getParameterList());
			self::assertEmpty($dependency->getConfiguratorList());
			self::assertEmpty($dependency->getInjectList());
		}

		public function testProxyFactory() {
			self::assertSame('moo', $this->container->create('moo'));
		}

		public function testLinkFactory() {
			self::assertSame('moo', $this->container->create('get-moo'));
		}

		public function testExceptionFactory() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('kaboom');
			$this->container->create('boom');
		}

		protected function setUp() {
			parent::setUp();
			$this->container->registerFactory(new CallbackFactory(function () {
				return 3.14;
			}, 'scalar'));
			$this->container->registerFactory(new CallbackFactory(function (string $foo) {
				return '3.14+' . $foo;
			}, 'scalar-parameter'));
			$this->container->registerFactory(new CallbackFactory(function (): string {
				return '3.14';
			}));
			$this->container->registerFactory(new CallbackFactory(function (IContainer $container) {
				return 'pi=' . $container->create('string');
			}, 'scalar-dependency'));
			$this->container->registerFactory(new InstanceFactory('instance', FooObject::class));
			$this->container->registerFactory(new InstanceFactory('instanced', FooObject::class, [], new FooObject()));
			$this->container->registerFactory(new InstanceFactory('instance-clone', FooObject::class, [], null, true));
			$this->container->registerFactory(new ProxyFactory('moo', FooObject::class, 'getMoo', []));
			$this->container->registerFactory(new LinkFactory('get-moo', 'moo'));
			$this->container->registerFactory(new ExceptionFactory('boom', EddeException::class, 'kaboom'));
		}
	}
