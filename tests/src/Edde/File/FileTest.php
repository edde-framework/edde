<?php
	declare(strict_types=1);
	namespace Edde\File;

	use Edde\TestCase;

	class FileTest extends TestCase {
		/**
		 * @throws IoException
		 */
		public function testSave() {
			$file = new File(__DIR__ . '/temp/file');
			$file->getDirectory()->create();
			$file->save('foo');
			self::assertTrue($file->exists(), 'file is not available');
		}

		/**
		 * @throws IoException
		 *
		 * @depends testSave
		 */
		public function testRename() {
			$file = new File(__DIR__ . '/temp/file');
			$file->rename('file2');
			self::assertSame('file2', $file->getName());
			self::assertTrue((new File(__DIR__ . '/temp/file2'))->exists());
		}

		/**
		 * @throws IoException
		 */
		public function testFileIterator() {
			$file = new File(__DIR__ . '/assets/text-file.txt');
			self::assertFalse($file->isOpen(), 'file is new, but it is already opened!');
			$file->open('r');
			self::assertSame([
				"abc\n",
				"cde\n",
				"fgh\n",
			], iterator_to_array($file));
			$file->close();
			self::assertFalse($file->isOpen(), 'file is still opened!');
		}
	}
