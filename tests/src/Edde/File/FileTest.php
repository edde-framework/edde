<?php
	declare(strict_types=1);
	namespace Edde\File;

	use Edde\Container\ContainerException;
	use Edde\TestCase;
	use ReflectionException;

	class FileTest extends TestCase {
		public function testGetFile() {
			$file = new File($name = __DIR__ . '/temp/file');
			self::assertSame($name, $file->getFile());
		}

		/**
		 * @throws FileException
		 */
		public function testOpenSameMode() {
			$file = new File(__DIR__ . '/assets/text-file.txt');
			$file->open('r');
			$file->open('r');
			self::assertTrue($file->isOpen());
		}

		/**
		 * @throws FileException
		 */
		public function testOpenDifferentMode() {
			$this->expectException(FileException::class);
			$this->expectExceptionMessage('Current file [/edde/tests/src/Edde/File/assets/text-file.txt] is already opened in different mode [r].');
			$file = new File(__DIR__ . '/assets/text-file.txt');
			$file->open('r');
			$file->open('w+');
		}

		/**
		 * @throws FileException
		 */
		public function testOpenException() {
			$this->expectException(FileException::class);
			$this->expectExceptionMessage('Cannot open file [this file does not exist (r)].');
			$file = new File('this file does not exist');
			$file->open('r');
		}

		/**
		 * @throws FileException
		 */
		public function testGetHandleException() {
			$this->expectException(FileException::class);
			$this->expectExceptionMessage('Current file [this file does not exist] is not opened or has been already closed.');
			$file = new File('this file does not exist');
			$file->getHandle();
		}

		/**
		 * @throws FileException
		 */
		public function testDeleteFileException() {
			$this->expectException(FileException::class);
			$this->expectExceptionMessage('Cannot delete opened [w+] file [/edde/tests/src/Edde/File/temp/file].');
			$file = new File(__DIR__ . '/temp/file');
			$file->save('foo');
			$file->open('w+');
			$file->delete();
		}

		/**
		 * @throws FileException
		 */
		public function testDelete() {
			$file = new File(__DIR__ . '/temp/delete-me-please');
			$file->save('foo');
			$file->delete();
			self::assertFalse($file->exists());
		}

		/**
		 * @throws FileException
		 */
		public function testSave() {
			$file = new File(__DIR__ . '/temp/file');
			$file->getDirectory()->create();
			$file->save('foo');
			self::assertTrue($file->exists(), 'file is not available');
		}

		/**
		 * @throws FileException
		 */
		public function testSaveException() {
			$this->expectException(FileException::class);
			$this->expectExceptionMessage('Cannot save content to already opened [r] file [/edde/tests/src/Edde/File/temp/file].');
			$file = new File(__DIR__ . '/temp/file');
			$file->save('foo');
			$file->open('r');
			$file->save('foo');
		}

		/**
		 * @throws FileException
		 */
		public function testRename() {
			$file = new File(__DIR__ . '/temp/file');
			$file->save('foo');
			$file->rename('file2');
			self::assertSame('file2', $file->getName());
			self::assertTrue((new File(__DIR__ . '/temp/file2'))->exists());
		}

		/**
		 * @throws FileException
		 */
		public function testRenameStrangeException() {
			$this->expectException(FileException::class);
			$this->expectExceptionMessage('Unable to rename file [/edde/tests/src/Edde/File/temp/boo] to [/edde/tests/src/Edde/File/temp/die/bitch!/this path is quite strange and will not probably work].');
			$file = new File(__DIR__ . '/temp/boo');
			$file->save('foo');
			$file->rename('die/bitch!/this path is quite strange and will not probably work');
		}

		/**
		 * @throws FileException
		 *
		 * @depends testSave
		 */
		public function testRenameException() {
			$this->expectException(FileException::class);
			$this->expectExceptionMessage('Cannot rename opened [r] file [/edde/tests/src/Edde/File/temp/file] to [file2].');
			$file = new File(__DIR__ . '/temp/file');
			$file->save('foo');
			$file->open('r');
			$file->rename('file2');
		}

		/**
		 * @throws FileException
		 */
		public function testTouchException() {
			$this->expectException(FileException::class);
			$this->expectExceptionMessage('Cannot touch file [/edde/tests/src/Edde/File/path/does/not/exists!].');
			$file = new File(__DIR__ . '/path/does/not/exists!');
			$file->touch();
		}

		/**
		 * @throws FileException
		 */
		public function testTouch() {
			$file = new File(__DIR__ . '/temp/touching-this-file');
			self::assertFalse($file->exists());
			$file->touch();
			self::assertTrue($file->exists());
		}

		/**
		 * @throws FileException
		 */
		public function testTouchOpenException() {
			$this->expectException(FileException::class);
			$this->expectExceptionMessage('Cannot touch already opened [w+] file [/edde/tests/src/Edde/File/temp/yep].');
			$file = new File(__DIR__ . '/temp/yep');
			$file->open('w+');
			$file->touch();
		}

		/**
		 * @throws FileException
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

		/**
		 * @throws FileException
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			parent::setUp();
			$temp = new Directory(__DIR__ . '/temp');
			$temp->purge();
		}
	}
