<?php
	namespace Edde\Common\File;

	use Edde\File\Directory;
	use Edde\TestCase;

	class DirectoryTest extends TestCase {
		public function testExists() {
			$directory = new Directory(__DIR__ . '/some/directory/here');
			self::assertFalse($directory->exists());
		}

		/**
		 * @throws \Edde\File\DirectoryException
		 * @throws \Edde\File\RealPathException
		 */
		public function testCreate() {
			$directory = new Directory(__DIR__ . '/some/directory/here');
			$directory->create(0655);
			self::assertSame(0655, $directory->getPermission());
			($directory = new Directory(__DIR__ . '/some'))->delete();
			self::assertFalse($directory->exists(), 'root directory has not been deleted!');
		}
	}
