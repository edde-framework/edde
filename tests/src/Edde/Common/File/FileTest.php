<?php
	namespace Edde\Common\File;

		use Edde\Api\File\Exception\RealPathException;
		use Edde\Ext\Test\TestCase;

		class FileTest extends TestCase {
			/**
			 * @throws RealPathException
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
		}
