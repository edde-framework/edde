<?php
	declare(strict_types=1);
	namespace Edde\Test;

	use Edde\Edde;

	class FooObject extends Edde {
		public $foo = 'foo';

		public function getMoo() {
			return 'moo';
		}
	}

	class BarObject extends Edde {
		public $bar = 'bar';
		public $fooObject;

		public function injectFooObject(FooObject $fooObject) {
			$this->fooObject = $fooObject;
		}
	}

	class FooBarObject extends Edde {
		/**
		 * @var FooObject
		 */
		public $fooObject;
		/**
		 * @var BarObject
		 */
		public $barObject;

		public function injectFooObject(FooObject $fooObject) {
			$this->fooObject = $fooObject;
		}

		public function injectBarObject(BarObject $barObject) {
			$this->barObject = $barObject;
		}
	}

	class AbstractDependencyObject extends Edde {
		/**
		 * @var FooObject
		 */
		public $fooObject;
		/**
		 * @var BarObject
		 */
		public $barObject;
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
		public function injectFooObject(FooObject $fooObject) {
			$this->fooObject = $fooObject;
		}

		public function injectBarObject(BarObject $barObject) {
			$this->barObject = $barObject;
		}
	}
