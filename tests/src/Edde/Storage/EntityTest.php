<?php
	declare(strict_types=1);
	namespace Edde\Storage;

	use Edde\TestCase;

	class EntityTest extends TestCase {
		public function testSimpleEntity() {
			$entity = new Entity('foo', [
				'a' => true,
				'b' => 'bar',
			]);
			self::assertSame('foo', $entity->getSchema());
			self::assertTrue($entity['a']);
			self::assertSame('bar', $entity['b']);
		}
	}
