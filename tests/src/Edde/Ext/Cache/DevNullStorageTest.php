<?php
	namespace Edde\Ext\Cache;

	use phpunit\framework\TestCase;

	class DevNullStorageTest extends TestCase {
		public function testCommon() {
			$storage = new DevNullCacheStorage();
			self::assertTrue($storage->save('foo', true));
			self::assertNull($storage->load('foo'));
			$storage->invalidate();
			self::assertNull($storage->load('foo'));
		}
	}
