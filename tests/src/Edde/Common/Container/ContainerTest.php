<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\IContainer;
	use Edde\Common\Container\Factory\CascadeFactory;
	use Edde\Common\ContainerTest\AlphaDependencyClass;
	use Edde\Common\ContainerTest\BetaDependencyClass;
	use Edde\Common\ContainerTest\LazyInjectTraitClass;
	use Edde\Common\ContainerTest\LazyMissmatch;
	use Edde\Common\ContainerTest\OnlySomeString;
	use Edde\Common\ContainerTest\SimpleClass;
	use Edde\Common\ContainerTest\SimpleDependency;
	use Edde\Common\ContainerTest\SimpleUnknownDependency;
	use Edde\Common\Strings\StringUtils;
	use Edde\Ext\Container\ContainerFactory;
	use Fallback\Foo\Bar\FooBar;
	use Fallback\Foo\Bar\IFooBar;
	use phpunit\framework\TestCase;

	require_once __DIR__ . '/assets.php';

	/**
	 * Tests related to dependency container.
	 */
	class ContainerTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testCommon() {
			/**
			 * this is testing ability to include external parameter of a unknown (unregistered) class
			 */
			self::assertInstanceOf(SimpleClass::class, $this->container->create(SimpleClass::class, new SimpleUnknownDependency(), 1));
		}

		public function testCache() {
			self::assertInstanceOf(SimpleClass::class, $this->container->create(SimpleClass::class, new SimpleUnknownDependency(), 1));
			self::assertInstanceOf(SimpleClass::class, $this->container->create(SimpleClass::class, new SimpleUnknownDependency(), 1));
		}

		public function testLazyInject() {
			$lazyClass = $this->container->create(LazyInjectTraitClass::class);
			self::assertInstanceOf(BetaDependencyClass::class, $lazyClass->foo());
			self::assertInstanceOf(AlphaDependencyClass::class, $lazyClass->bar());
		}

		public function testLazyMissmatch() {
			$this->expectException(ContainerException::class);
			$this->expectExceptionMessage('Lazy inject mismatch: parameter [$betaDependencyClass] of method [Edde\Common\ContainerTest\LazyMissmatch::lazyDependency()] must have a property [Edde\Common\ContainerTest\LazyMissmatch::$betaDependencyClass] with the same name as the parameter (for example protected $betaDependencyClass).');
			$this->container->create(LazyMissmatch::class);
		}

		public function testCascade() {
			$this->container->registerFactory(IFooBar::class, $this->container->inject(new CascadeFactory(function (OnlySomeString $onlySomeString, string $name) {
				if (interface_exists($name)) {
					$name = substr(StringUtils::extract($name, '\\', -1), 1);
				}
				$foo = $onlySomeString->gimmeString();
				return [
					"Fallback\\$foo\\Bar\\$name",
					"Fallback\\$foo\\Bar\\{$name}Service",
					"Fallback\\$foo\\$name",
					"Fallback\\$foo\\{$name}Service",
				];
			})));
			self::assertInstanceOf(FooBar::class, $instance = $this->container->create(IFooBar::class));
		}

		public function testScalar() {
			self::assertEquals('bar', $this->container->create('foo-bar'));
			self::assertEquals('bar', $this->container->call(function ($fooBar) {
				return $fooBar;
			}));
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				SimpleClass::class,
				SimpleDependency::class,
				LazyInjectTraitClass::class,
				BetaDependencyClass::class,
				'foo-bar' => function () {
					return 'bar';
				},
			]);
		}
	}
