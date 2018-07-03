<?php
	declare(strict_types=1);

	namespace Edde\Common;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\EddeException;
	use Edde\Test\BarObject;
	use Edde\Test\CompositeObject;
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
		 * @var CompositeObject
		 */
		protected $composite;

		public function testInstanceOfLazyInject() {
			self::assertInstanceOf(ILazyInject::class, $this->fooObject);
		}

		public function testWriteException() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Writing to the undefined/private/protected property [Edde\Test\FooObject::$thisWillThrowAnException].');
			/** @noinspection PhpUndefinedFieldInspection */
			$this->fooObject->thisWillThrowAnException = 'really!';
		}

		public function testReadException() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Reading from the undefined/private/protected property [Edde\Test\FooObject::$yesThisWillThrowAnException].');
			/** @noinspection PhpUnusedLocalVariableInspection */
			/** @noinspection PhpUndefinedFieldInspection */
			$willYouThrowAnException = $this->fooObject->yesThisWillThrowAnException;
		}

		public function testIsset() {
			self::assertFalse(isset($this->fooObject->yesThisWillThrowAnException));
			self::assertTrue(isset($this->fooObject->foo));
		}

		public function testObjectHash() {
			self::assertSame($this->fooObject->hash(), $this->fooObject->hash());
		}

		/**
		 * @codeCoverageIgnore
		 */
		protected function setUp() {
			$this->composite = new CompositeObject($this->fooObject = new FooObject(), $this->barObject = new BarObject($this->fooObject));
		}
	}
