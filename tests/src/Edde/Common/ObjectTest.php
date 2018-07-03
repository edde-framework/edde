<?php
	declare(strict_types=1);

	namespace Edde\Common;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Container\IAutowire;
	use Edde\Common\Object\Exception\PropertyReadException;
	use Edde\Common\Object\Exception\PropertyWriteException;
	use Edde\Test\BarObject;
	use Edde\Test\FooBarObject;
	use Edde\Test\FooObject;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	class ObjectTest extends TestCase {
		/**
		 * @var FooObject
		 */
		protected $fooObject;
		/**
		 * @var BarObject
		 */
		protected $barObject;
		/**
		 * @var FooBarObject
		 */
		protected $fooBarObject;

		public function testInstanceOf() {
			self::assertInstanceOf(IAutowire::class, $this->fooObject);
			self::assertInstanceOf(IConfigurable::class, $this->fooObject);
		}

		public function testWriteException() {
			$this->expectException(PropertyWriteException::class);
			$this->expectExceptionMessage('Writing to the undefined/private/protected property [Edde\Test\FooObject::$undefined].');
			/** @noinspection PhpUndefinedFieldInspection */
			$this->fooObject->undefined = true;
		}

		public function testReadException() {
			$this->expectException(PropertyReadException::class);
			$this->expectExceptionMessage('Reading from the undefined/private/protected property [Edde\Test\FooObject::$undefined].');
			/** @noinspection PhpUndefinedFieldInspection */
			$this->fooObject->undefined;
		}

		public function testIsset() {
			/** @noinspection PhpUndefinedFieldInspection */
			self::assertFalse(isset($this->fooObject->undefined));
			self::assertTrue(isset($this->fooObject->foo));
		}

		/**
		 * @codeCoverageIgnore
		 */
		protected function setUp() {
			$this->fooBarObject = new FooBarObject($this->fooObject = new FooObject(), $this->barObject = new BarObject($this->fooObject));
		}
	}
