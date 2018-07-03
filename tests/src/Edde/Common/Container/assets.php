<?php
	declare(strict_types = 1);

	namespace Edde\Common\ContainerTest {

		use Edde\Api\Container\ILazyInject;
		use Edde\Common\AbstractObject;
		use Edde\Common\Cache\AbstractCacheStorage;

		class SimpleDependency {
		}

		class SimpleUnknownDependency {
		}

		class SimpleClass {
			public function __construct(SimpleDependency $simpleDependency, SimpleUnknownDependency $simpleUnknownDependency, $dummyOne) {
			}
		}

		/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
		class AlphaDependencyClass {
		}

		/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
		class BetaDependencyClass {
		}

		/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
		class TestCommonClass {
			private $foo;
			private $bar;
			private $cloned = false;

			public function __construct($foo, $bar) {
				$this->foo = $foo;
				$this->bar = $bar;
			}

			public function getFoo() {
				return $this->foo;
			}

			public function getBar() {
				return $this->bar;
			}

			public function isCloned() {
				return $this->cloned;
			}

			public function __clone() {
				$this->cloned = true;
			}
		}

		/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
		class RecursiveClass {
			public function __construct(RecursiveClass $recursiveClass) {
			}
		}

		/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
		class TestMagicFactory {
		}

		/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
		class MagicFactory {
			private $flag = false;

			public function hasFlag() {
				return $this->flag;
			}

			public function __invoke() {
				$this->flag = true;
				return new TestMagicFactory();
			}
		}

		/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
		class DummyCacheStorage extends AbstractCacheStorage {
			public function save(string $id, $save) {
			}

			public function load($id) {
			}

			public function invalidate() {
			}

			protected function prepare() {
			}
		}

		/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
		class LazyInjectTraitClass extends AbstractObject implements ILazyInject {
			/**
			 * @var BetaDependencyClass
			 */
			private $betaDependencyClass;
			/**
			 * @var AlphaDependencyClass
			 */
			private $alphaDependencyClass;

			/**
			 * @param BetaDependencyClass $betaDependencyClass
			 * @param AlphaDependencyClass $alphaDependencyClass
			 */
			public function lazyDependency(BetaDependencyClass $betaDependencyClass, AlphaDependencyClass $alphaDependencyClass) {
				$this->betaDependencyClass = $betaDependencyClass;
				$this->alphaDependencyClass = $alphaDependencyClass;
			}

			public function foo() {
				return $this->betaDependencyClass;
			}

			public function bar() {
				return $this->alphaDependencyClass;
			}
		}

		/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
		class LazyMissmatch extends AbstractObject implements ILazyInject {
			public function lazyDependency(BetaDependencyClass $betaDependencyClass) {
			}
		}

		class OnlySomeString {
			public function gimmeString() {
				return 'Foo';
			}
		}
	}

	namespace Fallback\Foo\Bar {
		interface IFooBar {
		}

		class FooBar implements IFooBar {
		}
	}

	namespace Fallback\Foo {

		use Fallback\Foo\Bar\IFooBar;

		class FooBarService implements IFooBar {
		}
	}
