<?php
	declare(strict_types=1);
	namespace Edde\Test;

		use Edde\Common\Object\Object;

		/**
		 * just fake function
		 */
		function foo(FooObject $fooObject): FooObject {
			return $fooObject;
		}

		class FooObject extends Object {
			public $foo = 'foo';

			public function getMoo() {
				return 'moo';
			}
		}

		class BarObject extends Object {
			public $bar = 'bar';
			public $fooObject;

			public function __construct(FooObject $fooObject) {
				$this->fooObject = $fooObject;
			}
		}

		class FooBarObject extends Object {
			/**
			 * @var FooObject
			 */
			public $fooObject;
			/**
			 * @var BarObject
			 */
			public $barObject;

			public function __construct(FooObject $fooObject, BarObject $barObject) {
				$this->fooObject = $fooObject;
				$this->barObject = $barObject;
			}
		}

		class AbstractDependencyObject extends Object {
			/**
			 * @var FooObject
			 */
			public $fooObject;
			/**
			 * @var BarObject
			 */
			public $barObject;
		}

		class ConstructorDependencyObject extends AbstractDependencyObject {
			public function __construct(FooObject $fooObject, BarObject $barObject) {
				$this->fooObject = $fooObject;
				$this->barObject = $barObject;
			}
		}

		class InjectDependencyObject extends AbstractDependencyObject {
			public function injectFooObject(FooObject $fooObject) {
				$this->fooObject = $fooObject;
			}

			public function injectBarObject(BarObject $barObject) {
				$this->barObject = $barObject;
			}
		}

		class AutowireDependencyObject extends AbstractDependencyObject {
			public function lazyFooObject(FooObject $fooObject) {
				$this->fooObject = $fooObject;
			}

			public function lazyBarObject(BarObject $barObject) {
				$this->barObject = $barObject;
			}
		}