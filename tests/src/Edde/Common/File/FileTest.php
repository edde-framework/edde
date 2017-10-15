<?php
	namespace Edde\Common\File;

		use Edde\Ext\Test\TestCase;

		class FileTest extends TestCase {
			public function testFile() {
				$file = new File(__DIR__ . '/temp/file');
				$directory = $file->getDirectory();
				$directory->create();
				$directory->delete();
			}
		}
