<?php
	declare(strict_types=1);
	namespace Edde\Crate;

	use Edde\Schema\Attribute;
	use Edde\TestCase;

	class PropertyTest extends TestCase {
		public function testGetValue() {
			$property = new Property(new Attribute((object)[]));
			$property->setDefault('default');
			$property->setValue('value');
			self::assertSame('value', $property->getValue());
			self::assertSame('default', $property->getDefault());
			self::assertSame('value', $property->get());
		}

		public function testDefaultValue() {
			$property = new Property(new Attribute((object)[]));
			self::assertSame('bar', $property->get('bar'));
		}

		public function testIsNotDirty() {
			$property = new Property(new Attribute((object)[]));
			$property->setDefault('bar');
			self::assertFalse($property->isDirty(), 'property should NOT be dirty with just default value!');
			$property->setValue('bar');
			self::assertFalse($property->isDirty(), 'property should NOT be dirty when values are same!');
		}

		public function testIsDirty() {
			$property = new Property(new Attribute((object)[]));
			$property->setValue('bar');
			self::assertTrue($property->isDirty(), 'property should BE dirty with set value!');
			self::assertSame('bar', $property->getValue());
			self::assertSame('bar', $property->get());
		}

		public function testIsDirtyWithFalse() {
			$property = new Property(new Attribute((object)[]));
			$property->setValue(false);
			self::assertTrue($property->isDirty(), 'property should be dirty!');
			self::assertFalse($property->get(), 'value of property should be boolean false!');
		}

		public function testCommit() {
			$property = new Property(new Attribute((object)[]));
			$property->setValue('bar');
			self::assertSame('bar', $property->get());
			self::assertTrue($property->isDirty());
			$property->commit();
			self::assertFalse($property->isDirty(), 'property is still dirty even after commit!');
			self::assertSame('bar', $property->get());
			self::assertSame(null, $property->getValue());
		}

		public function testIsEmpty() {
			$property = new Property(new Attribute((object)[]));
			self::assertTrue($property->isEmpty(), 'property is NOT empty by default');
			$property->setDefault('value');
			self::assertFalse($property->isEmpty(), 'property has a default value, but it is still empty!');
			$property->setValue(null);
			self::assertTrue($property->isEmpty(), 'property is NOT empty when there is set NULL value');
		}
	}
