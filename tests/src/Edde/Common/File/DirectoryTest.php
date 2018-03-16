<?php
	namespace Edde\Common\File;

	use Edde\Exception\File\DirectoryException;
	use Edde\Exception\File\RealPathException;
	use Edde\TestCase;

	class DirectoryTest extends TestCase {
		public function testExists() {
			$directory = new Directory(__DIR__ . '/some/directory/here');
			self::assertFalse($directory->exists());
		}

		/**
		 * @throws \Edde\Exception\File\DirectoryException
		 * @throws \Edde\Exception\File\RealPathException
		 */
		public function testCreate() {
			$directory = new Directory(__DIR__ . '/some/directory/here');
			$directory->create(0655);
			self::assertSame(0655, $directory->getPermission());
			($directory = new Directory(__DIR__ . '/some'))->delete();
			self::assertFalse($directory->exists(), 'root directory has not been deleted!');
		}
	}
