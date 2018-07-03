<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	use foo\loadMe;
	use phpunit\framework\TestCase;

	/**
	 * Formal test because autoloader is core dependency; nothing is runnable without it.
	 */
	class AutoloaderTest extends TestCase {
		public function testAutoloader() {
			$fooAutoloader = Autoloader::register('foo', __DIR__);
			$barAutoloader = Autoloader::register('bar', __DIR__);
			self::assertFalse($barAutoloader(loadMe::class));
			self::assertTrue($fooAutoloader(loadMe::class));
			self::assertInstanceOf(loadMe::class, new loadMe());
		}
	}
