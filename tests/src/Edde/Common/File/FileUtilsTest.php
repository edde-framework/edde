<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use phpunit\framework\TestCase;

	class FileUtilsTest extends TestCase {
		public function testCommon() {
			self::assertEquals('1.0K', FileUtils::humanSize(1024, 1));
			self::assertEquals('2.38K', FileUtils::humanSize(2439, 2));
			self::assertEquals('28.6107M', FileUtils::humanSize(30000459, 4));
		}

		public function testFileSize() {
			self::assertEquals(37, FileUtils::size(__DIR__ . '/bar/c'));
			self::assertEquals(0, FileUtils::size(__DIR__ . '/foo/a'));
		}
	}
