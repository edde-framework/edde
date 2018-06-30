<?php
	declare(strict_types=1);
	namespace Edde;

	use ConstructorClass;
	use Edde\Configurable\IConfigurable;
	use Edde\Container\IAutowire;
	use Edde\Test\BarObject;
	use Edde\Test\FooBarObject;
	use Edde\Test\FooObject;
	use PHPUnit\Framework\TestCase;

	class ObjectTest extends TestCase {
		/** @var FooObject */
		protected $fooObject;
		/** @var BarObject */
		protected $barObject;
		/** @var FooBarObject */
		protected $fooBarObject;

		public function testInstanceOf() {
			self::assertInstanceOf(IAutowire::class, $this->fooObject);
			self::assertInstanceOf(IConfigurable::class, $this->fooObject);
		}

		public function testWriteException() {
			$this->expectException(ObjectException::class);
			$this->expectExceptionMessage('Writing to the undefined/private/protected property [Edde\Test\FooObject::$undefined].');
			/** @noinspection PhpUndefinedFieldInspection */
			$this->fooObject->undefined = true;
		}

		public function testReadException() {
			$this->expectException(ObjectException::class);
			$this->expectExceptionMessage('Reading from the undefined/private/protected property [Edde\Test\FooObject::$undefined].');
			/** @noinspection PhpUndefinedFieldInspection */
			$this->fooObject->undefined;
		}

		public function testIsset() {
			/** @noinspection PhpUndefinedFieldInspection */
			self::assertFalse(isset($this->fooObject->undefined));
			self::assertTrue(isset($this->fooObject->foo));
		}

		public function testClone() {
			$class = new ConstructorClass('boo');
			self::assertFalse($class->isSetup());
			$class->setup();
			self::assertTrue($class->isSetup());
			$clone = clone $class;
			self::assertFalse($clone->isSetup());
		}

		/**
		 * @codeCoverageIgnore
		 */
		protected function setUp() {
			$this->fooBarObject = new FooBarObject();
			$this->fooBarObject->injectFooObject($this->fooObject = new FooObject());
			$this->fooBarObject->injectBarObject($this->barObject = new BarObject());
		}
	}
