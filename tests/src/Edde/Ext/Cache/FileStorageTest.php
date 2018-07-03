<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Cache;

	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\Cache\CacheDirectory;
	use Edde\Common\File\FileUtils;
	use phpunit\framework\TestCase;

	class FileStorageTest extends TestCase {
		/**
		 * @var ICacheStorage
		 */
		protected $cacheStorage;

		public static function tearDownAfterClass() {
			FileUtils::delete(__DIR__ . '/cache');
		}

		public function testCommon() {
			self::assertFalse($this->cacheStorage->isUsed());
			self::assertEquals(1, $this->cacheStorage->save('foo', 1));
			self::assertEquals(2, $this->cacheStorage->save('bar', 2));
			self::assertEquals(1, $this->cacheStorage->load('foo'));
			self::assertEquals(2, $this->cacheStorage->load('bar'));
			$this->cacheStorage->save('bar', null);
			self::assertNull($this->cacheStorage->load('bar'));
		}

		public function testCommon2() {
			self::assertFalse($this->cacheStorage->isUsed());
			self::assertEquals(1, $this->cacheStorage->load('foo'));
			$this->cacheStorage->invalidate();
			self::assertNull($this->cacheStorage->load('foo'));
		}

		protected function setUp() {
			$this->cacheStorage = new FileCacheStorage(__DIR__);
			$this->cacheStorage->lazyCacheDirectory(new CacheDirectory(__DIR__ . '/cache'));
		}
	}
