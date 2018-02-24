<?php
	namespace Edde\Common\File;

	use Edde\Api\File\Exception\DirectoryException;
	use Edde\Api\File\Exception\RealPathException;
	use Edde\Ext\Test\TestCase;

	class DirectoryTest extends TestCase {
		public function testExists() {
			$directory = new Directory(__DIR__ . '/some/directory/here');
			self::assertFalse($directory->exists());
		}

		/**
		 * @throws DirectoryException
		 * @throws RealPathException
		 */
		public function testCreate() {
			$directory = new Directory(__DIR__ . '/some/directory/here');
			$directory->create(0655);
			self::assertSame(0655, $directory->getPermission());
			($directory = new Directory(__DIR__ . '/some'))->delete();
			self::assertFalse($directory->exists(), 'root directory has not been deleted!');
		}
	}
