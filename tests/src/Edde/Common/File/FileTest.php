<?php
	namespace Edde\Common\File;

	use Edde\Exception\File\RealPathException;
	use Edde\TestCase;

	class FileTest extends TestCase {
		/**
		 * @throws \Edde\Exception\File\RealPathException
		 */
		public function testFile() {
			$file = File::create(__DIR__ . '/temp/file');
			$file->save('foo');
			self::assertTrue($file->isAvailable(), 'file is not available');
			$directory = $file->getDirectory();
			$directory->create();
			$directory->delete();
			self::assertFalse($directory->exists(), 'directory has not been deleted!');
		}

		public function testFileIterator() {
			$file = File::create(__DIR__ . '/assets/text-file.txt');
			self::assertFalse($file->isOpen(), 'file is new, but it is already opened!');
			self::assertSame([
				"abc\n",
				"cde\n",
				"fgh\n",
			], iterator_to_array($file));
			self::assertFalse($file->isOpen(), 'file is still opened!');
		}
	}
