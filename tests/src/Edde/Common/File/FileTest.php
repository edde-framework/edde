<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use Edde\Api\File\IFile;
	use phpunit\framework\TestCase;

	class FileTest extends TestCase {
		/**
		 * @var IFile
		 */
		protected $file;

		public function testOpenForWrite() {
			$this->file->delete();
			self::assertFalse($this->file->isOpen());
			self::assertFalse($this->file->isAvailable());
			$this->file->write("foo\n");
			$this->file->write("bar\n");
			self::assertTrue($this->file->isOpen());
			self::assertTrue($this->file->isAvailable());
			$this->file->close();
			self::assertFalse($this->file->isOpen());
			self::assertTrue($this->file->isAvailable());
		}

		public function testFileContent() {
			self::assertTrue($this->file->isAvailable());
			self::assertEquals("foo\nbar\n", $this->file->get());
		}

		public function testIterator() {
			self::assertFalse($this->file->isOpen());
			self::assertTrue($this->file->isAvailable());
			$lines = [];
			foreach ($this->file as $line) {
				$lines[] = $line;
			}
			self::assertEquals([
				"foo\n",
				"bar\n",
			], $lines);
			self::assertFalse($this->file->isOpen());
		}

		protected function setUp() {
			$this->file = new File(__DIR__ . '/temp/file.txt');
		}
	}
