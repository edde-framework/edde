<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class CacheTest extends TestCase {
		public function testCommon() {
			$cache = new Cache(new \TestCacheStorage(), self::class);
			self::assertEquals('3.14', $cache->save('foo', '3.14'));
			self::assertEquals('3.14', $cache->load('foo'));
			self::assertEquals('14.3', $cache->load('bar', '14.3'));
			self::assertEquals('bar.14.3', $cache->load('bar', function () {
				return 'bar.14.3';
			}));
			$cache->invalidate();
			self::assertNull($cache->load('foo'));
			$count = 0;
			self::assertEquals('foo', $cache->callback('boo', function () use (&$count) {
				$count++;
				return 'foo';
			}));
			self::assertEquals('foo', $cache->callback('boo', function () use (&$count) {
				$count++;
				return 'foo';
			}));
			self::assertEquals(1, $count);
		}
	}
