<?php
	namespace Edde\Common\Crate;

		use Edde\Ext\Test\TestCase;

		class PropertyTest extends TestCase {
			public function testName() {
				self::assertSame('Strange Name of The Property', (new Property('Strange Name of The Property'))->getName());
			}

			public function testGetValue() {
				$property = new Property('foo');
				$property->setDefault('default');
				$property->setValue('value');
				self::assertSame('value', $property->getValue());
				self::assertSame('default', $property->getDefault());
				self::assertSame('value', $property->get());
			}

			public function testDefaultValue() {
				$property = new Property('foo');
				self::assertSame('bar', $property->get('bar'));
			}

			public function testIsNotDirty() {
				$property = new Property('foo');
				$property->setDefault('bar');
				self::assertFalse($property->isDirty(), 'property should NOT be dirty with just default value!');
				$property->setValue('bar');
				self::assertFalse($property->isDirty(), 'property should NOT be dirty when values are same!');
			}

			public function testIsDirty() {
				$property = new Property('foo');
				$property->setValue('bar');
				self::assertTrue($property->isDirty(), 'property should BE dirty with set value!');
				self::assertSame('bar', $property->getValue());
				self::assertSame('bar', $property->get());
			}

			public function testCommit() {
				$property = new Property('foo');
				$property->setValue('bar');
				self::assertSame('bar', $property->get());
				self::assertTrue($property->isDirty());
				$property->commit();
				self::assertFalse($property->isDirty(), 'property is still dirty even after commit!');
				self::assertSame('bar', $property->get());
				self::assertSame(null, $property->getValue());
			}

			public function testIsEmpty() {
				$property = new Property('foo');
				self::assertTrue($property->isEmpty(), 'property is NOT empty by default');
				$property->setDefault('value');
				self::assertFalse($property->isEmpty(), 'property has a default value, but it is still empty!');
				$property->setValue(null);
				self::assertTrue($property->isEmpty(), 'property is NOT empty when there is set NULL value');
			}
		}
