<?php
	declare(strict_types=1);
	namespace Edde\File;

	use Edde\TestCase;
	use function sort;

	class DirectoryTest extends TestCase {
		public function testGetPath() {
			$directory = new Directory($dir = __DIR__ . '/some/directory/here');
			self::assertSame($dir, $directory->getPath());
		}

		public function testGetName() {
			$directory = new Directory(__DIR__ . '/some/directory/here');
			self::assertSame('here', $directory->getName());
		}

		public function testDirectory() {
			$directory = new Directory(__DIR__ . '/temp');
			$directory = $directory->directory('directory-name');
			self::assertFalse($directory->exists());
			self::assertSame('/edde/tests/src/Edde/File/temp/directory-name', $directory->getPath());
		}

		public function testParent() {
			$directory = new Directory($dir = __DIR__ . '/temp');
			$directory = $directory->directory('directory-name');
			$parent = $directory->parent();
			self::assertSame($parent, $directory->parent());
			self::assertSame($dir, $parent->getPath());
		}

		/**
		 * @throws FileException
		 */
		public function testSave() {
			$directory = new Directory($dir = __DIR__ . '/temp');
			$file = $directory->save('poo', 'poo');
			self::assertSame($dir . '/poo', $file->getFile());
			self::assertTrue($file->exists());
		}

		public function testGetFiles() {
			$actual = [];
			$expect = [
				'/edde/tests/src/Edde/File/assets/dir1/file-2.txt',
				'/edde/tests/src/Edde/File/assets/dir1/file-1',
				'/edde/tests/src/Edde/File/assets/text-file.txt',
			];
			/** @var $directory IDirectory */
			$directory = new Directory(__DIR__ . '/assets');
			foreach ($directory->getFiles() as $splFileInfo) {
				$actual[] = $splFileInfo->getRealPath();
			}
			sort($expect);
			sort($actual);
			self::assertSame($expect, $actual);
		}

		public function testFileIterator() {
			$actual = [];
			$expect = [
				new File('/edde/tests/src/Edde/File/assets/dir1/file-2.txt'),
				new File('/edde/tests/src/Edde/File/assets/dir1/file-1'),
				new File('/edde/tests/src/Edde/File/assets/text-file.txt'),
			];
			/** @var $directory IDirectory */
			$directory = new Directory(__DIR__ . '/assets');
			foreach ($directory as $file) {
				$actual[] = $file;
			}
			sort($expect);
			sort($actual);
			self::assertEquals($expect, $actual);
		}

		/**
		 * @throws FileException
		 */
		public function testCreateException() {
			$this->expectException(FileException::class);
			$this->expectExceptionMessage('Cannot create directory [/] with [777].');
			$directory = new Directory('/');
			$directory->create();
		}

		public function testNegativeExists() {
			$directory = new Directory(__DIR__ . '/some/directory/here');
			self::assertFalse($directory->exists());
		}

		public function testPositiveExists() {
			$directory = new Directory(__DIR__ . '/assets');
			self::assertTrue($directory->exists());
		}

		/**
		 * @throws FileException
		 */
		public function testCreate() {
			$directory = new Directory(__DIR__ . '/some/directory/here');
			$directory->create(0655);
			self::assertSame(0655, $directory->getPermission());
			($directory = new Directory(__DIR__ . '/some'))->delete();
			self::assertFalse($directory->exists(), 'root directory has not been deleted!');
		}

		/**
		 * @throws FileException
		 */
		public function testPurge() {
			/** @var $directory IDirectory */
			$directory = new Directory(__DIR__ . '/temp/dir');
			$directory->create();
			$directory->save('boo', 'yep');
			$actual = [];
			$expect = [
				'/edde/tests/src/Edde/File/temp/dir/boo',
			];
			foreach ($directory->getFiles() as $splFileInfo) {
				$actual[] = $splFileInfo->getRealPath();
			}
			self::assertEquals($expect, $actual);
			$directory->purge();
			$actual = [];
			foreach ($directory->getFiles() as $splFileInfo) {
				$actual[] = $splFileInfo->getRealPath();
			}
			self::assertEmpty($actual);
		}

		/**
		 * @throws FileException
		 */
		public function testPurgeNotExist() {
			/** @var $directory IDirectory */
			$directory = new Directory(__DIR__ . '/temp/dir2');
			$directory->purge();
			$actual = [];
			foreach ($directory->getFiles() as $splFileInfo) {
				$actual[] = $splFileInfo->getRealPath();
			}
			self::assertTrue($directory->exists());
			self::assertEmpty($actual);
		}

		protected function setUp() {
			parent::setUp();
			$temp = new Directory(__DIR__ . '/temp');
			$temp->create();
		}

		protected function tearDown() {
			parent::tearDown();
			$temp = new Directory(__DIR__ . '/temp');
			$temp->delete();
		}
	}
