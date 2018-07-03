<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use phpunit\framework\TestCase;

	class SchemaPropertyTest extends TestCase {
		public function testDirty() {
			$schema = new Schema('dummy');
			$property = new SchemaProperty($schema, 'foo', 'int');
			self::assertFalse($property->isDirty('1', 1), 'string integer is dirty!');
			$property = new SchemaProperty($schema, 'foo', 'float');
			self::assertFalse($property->isDirty(3.141592, 3.141592), 'float is dirty!');
			self::assertTrue($property->isDirty(3.141592, 3.141593), 'float is NOT dirty!');
			$property = new SchemaProperty($schema, 'foo');
			self::assertFalse($property->isDirty('100', 100), 'integer string is dirty');
			$property = new SchemaProperty($schema, 'foo', 'string[]');
			$property->array();
			self::assertFalse($property->isDirty(['100'], ['100']), 'string array is dirty');
			$property = new SchemaProperty($schema, 'foo', 'bool');
			self::assertFalse($property->isDirty('on', true), 'bool check is dirty');
			self::assertFalse($property->isDirty('on', '1'), 'bool check is dirty');
		}
	}
