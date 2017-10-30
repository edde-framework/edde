<?php
	declare(strict_types=1);
	require_once __DIR__ . '/../Schema/assets.php';

	//	namespace Edde\Test {
	//
	//
	//		use Edde\Common\Object\Object;
	//
	//		/**
	//		 * just fake function
	//		 */
	//		function foo(FooObject $fooObject): FooObject {
	//			return $fooObject;
	//		}
	//
	//		class FooObject extends Object {
	//			public $foo = 'foo';
	//
	//			public function getMoo() {
	//				return 'moo';
	//			}
	//		}
	//
	//		class BarObject extends Object {
	//			public $bar = 'bar';
	//			public $fooObject;
	//
	//			public function __construct(FooObject $fooObject) {
	//				$this->fooObject = $fooObject;
	//			}
	//		}
	//
	//		class FooBarObject extends Object {
	//			/**
	//			 * @var FooObject
	//			 */
	//			public $fooObject;
	//			/**
	//			 * @var BarObject
	//			 */
	//			public $barObject;
	//
	//			public function __construct(FooObject $fooObject, BarObject $barObject) {
	//				$this->fooObject = $fooObject;
	//				$this->barObject = $barObject;
	//			}
	//		}
	//
	//		class AbstractDependencyObject extends Object {
	//			/**
	//			 * @var FooObject
	//			 */
	//			public $fooObject;
	//			/**
	//			 * @var BarObject
	//			 */
	//			public $barObject;
	//		}
	//
	//		class ConstructorDependencyObject extends AbstractDependencyObject {
	//			public function __construct(FooObject $fooObject, BarObject $barObject) {
	//				$this->fooObject = $fooObject;
	//				$this->barObject = $barObject;
	//			}
	//		}
	//
	//		class InjectDependencyObject extends AbstractDependencyObject {
	//			public function injectFooObject(FooObject $fooObject) {
	//				$this->fooObject = $fooObject;
	//			}
	//
	//			public function injectBarObject(BarObject $barObject) {
	//				$this->barObject = $barObject;
	//			}
	//		}
	//
	//		class AutowireDependencyObject extends AbstractDependencyObject {
	//			public function lazyFooObject(FooObject $fooObject) {
	//				$this->fooObject = $fooObject;
	//			}
	//
	//			public function lazyBarObject(BarObject $barObject) {
	//				$this->barObject = $barObject;
	//			}
	//		}
	//
	//		interface AbstractGuidSchema {
	//			/**
	//			 * @schema primary
	//			 */
	//			function guid(): string;
	//		}
	//
	//		interface SimpleSchema extends AbstractGuidSchema {
	//			/**
	//			 * @schema
	//			 */
	//			function name(): string;
	//
	//			/**
	//			 * @schema
	//			 */
	//			function optional(): ?string;
	//
	//			/**
	//			 * @schema
	//			 */
	//			function value(): ?float;
	//
	//			/**
	//			 * @schema
	//			 */
	//			function date(): ?\DateTime;
	//
	//			/**
	//			 * @schema
	//			 */
	//			function question(): ?bool;
	//		}
	//
	//		interface FooSchema extends AbstractGuidSchema {
	//			/**
	//			 * @schema unique
	//			 */
	//			public function name(): string;
	//
	//			/**
	//			 * @schema
	//			 */
	//			public function label(): ?string;
	//		}
	//
	//		interface BarSchema extends AbstractGuidSchema {
	//			/**
	//			 * @schema unique
	//			 */
	//			public function name(): string;
	//		}
	//
	//		/**
	//		 * @relation
	//		 */
	//		interface FooBarSchema {
	//			/**
	//			 * @schema primary
	//			 */
	//			public function foo(?FooSchema $guid);
	//
	//			/**
	//			 * @schema primary
	//			 */
	//			public function bar(BarSchema $guid);
	//		}
	//	}
	//	namespace Foo {
	//
	//		use Edde\Api\Element\IElement;
	//
	//		class FooService {
	//			public function fooAction(IElement $element) {
	//				$element->setMeta('done', true);
	//			}
	//		}
	//	}
