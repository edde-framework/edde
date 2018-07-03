<?php
	declare(strict_types=1);

	namespace Edde\Test {

		use Edde\Api\Router\IRequest;
		use Edde\Common\Object\Object;
		use Edde\Common\Request\Message;
		use Edde\Common\Router\AbstractRouter;
		use Edde\Common\Router\Request;

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

		class TestRouter extends AbstractRouter {
			public function canHandle(): bool {
				return true;
			}

			public function createRequest(): IRequest {
				return new Request(new Message('foo.foo-service/foo-action'));
			}
		}
	}

	namespace Foo {

		use Edde\Api\Protocol\IElement;

		class FooService {
			public function fooAction(IElement $element) {
				$element->setMeta('done', true);
			}
		}
	}
