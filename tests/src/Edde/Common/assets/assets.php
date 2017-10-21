<?php
	declare(strict_types=1);
	namespace Edde\Test {

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

		interface AbstractGuidSchema {
			/**
			 * @schema-property primary
			 */
			function guid(): string;
		}

		/**
		 * @schema-alias another name for this schema
		 */
		interface AnotherSchema extends AbstractGuidSchema {
			/**
			 * @schema-property
			 */
			function justName(): int;
		}

		interface MoreSchemasLikeThis extends AbstractGuidSchema {
			/**
			 * @schema-property unique
			 */
			function foo(): string;
		}

		interface ThisWillRepresentSchema extends AbstractGuidSchema {
			/**
			 * @schema-property
			 */
			function property(): string;

			/**
			 * @return null|string
			 */
			function something(): ?string;

			/**
			 * link creates a simple relation ($this to AnotherSchema)
			 *
			 * @schema-link
			 */
			function linkToSchema(): AnotherSchema;

			/**
			 * relation creates a M:N relation ($this could have multiple AnotherSchemas)
			 *
			 * @schema-relation
			 */
			function multiLink(): AnotherSchema;
		}
	}
	namespace Foo {

		use Edde\Api\Element\IElement;

		class FooService {
			public function fooAction(IElement $element) {
				$element->setMeta('done', true);
			}
		}
	}
