<?php
	declare(strict_types=1);
	namespace Edde\Factory;

	use Edde\TestCase;
	use ShittyInjectClass;
	use ShittyInjectTypehintClass;
	use ShittyInjectVisibilityClass;

	class ClassFactoryTest extends TestCase {
		/**
		 * @throws FactoryException
		 */
		public function testBadInjectException() {
			$this->expectException(FactoryException::class);
			$this->expectExceptionMessage('Class [ShittyInjectClass] must have property [$userSchema] of the same name as parameter in method [ShittyInjectClass::injectSomething(..., UserSchema $userSchema, ...)].');
			$factory = new ClassFactory();
			$factory->getReflection($this->container, ShittyInjectClass::class);
		}

		/**
		 * @throws FactoryException
		 */
		public function testBadInject3Exception() {
			$this->expectException(FactoryException::class);
			$this->expectExceptionMessage('Class [ShittyInjectTypehintClass] must have property [$userSchema] with class type hint in method [ShittyInjectTypehintClass::injectSomething(..., $userSchema, ...)].');
			$factory = new ClassFactory();
			$factory->getReflection($this->container, ShittyInjectTypehintClass::class);
		}

		/**
		 * @throws FactoryException
		 */
		public function testBadInject2Exception() {
			$this->expectException(FactoryException::class);
			$this->expectExceptionMessage('Method [ShittyInjectVisibilityClass::injectSomething()] must be public.');
			$factory = new ClassFactory();
			$factory->getReflection($this->container, ShittyInjectVisibilityClass::class);
		}
	}
