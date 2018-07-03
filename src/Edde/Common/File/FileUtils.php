<?php
	declare(strict_types=1);

	namespace Edde\Common\File;

	use Edde\Api\File\DirectoryException;
	use Edde\Api\File\FileException;
	use Edde\Api\Url\IUrl;
	use Edde\Api\Url\UrlException;
	use Edde\Common\Object;
	use Edde\Common\Url\Url;

	class FileUtils extends Object {
		static protected $mimeTypeList = [
			'xml'   => 'text/xml',
			'xhtml' => 'application/xhtml+xml',
			'json'  => 'application/json',
			'csv'   => 'text/csv',
			'php'   => 'text/x-php',
		];

		/**
		 * convert size to human readable size
		 *
		 * @param int $size
		 * @param int $decimals
		 *
		 * @return string
		 */
		static public function humanSize(int $size, int $decimals = 2): string {
			$sizeList = 'BKMGTP';
			$factor = floor((strlen((string)$size) - 1) / 3);
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			return sprintf("%.{$decimals}f", $size / pow(1024, $factor)) . @$sizeList[(int)$factor];
		}

		/**
		 * return mime type of the given file; this method is a bit more clever
		 *
		 * @param string $file
		 *
		 * @return string
		 * @throws FileException
		 * @throws UrlException
		 */
		static public function mime(string $file) {
			if (is_file($file) === false) {
				throw new FileException(sprintf('The given file [%s] is not a file.', $file));
			}
			$url = Url::create($file);
			if (isset(self::$mimeTypeList[$type = $url->getExtension()])) {
				return self::$mimeTypeList[$type];
			}
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			$info = @getimagesize($file); // @ - files smaller than 12 bytes causes read error
			if (isset($info['mime'])) {
				return $info['mime'];
			} else if (extension_loaded('fileinfo')) {
				$type = preg_replace('#[\s;].*\z#', '', finfo_file(finfo_open(FILEINFO_MIME), $file));
			} else if (function_exists('mime_content_type')) {
				$type = mime_content_type($file);
			}
			return isset($type) && preg_match('#^\S+/\S+\z#', $type) ? $type : 'application/octet-stream';
		}

		/**
		 * generate temporary file name; it uses system temp dir (sys_get_temp_dir())
		 *
		 * @param string|null $prefix
		 *
		 * @return string
		 */
		static public function generateTempName($prefix = null) {
			return tempnam(sys_get_temp_dir(), $prefix);
		}

		/**
		 * recreate the given directory with respect to preserve permissions of a given folder
		 *
		 * @param string   $path
		 * @param int|null $permissions
		 *
		 * @throws FileException
		 */
		static public function recreate($path, $permissions = null) {
			if ($permissions === null) {
				$permissions = 0777;
				if (file_exists($path)) {
					$permissions = self::getPermission($path);
				}
			}
			self::delete($path);
			self::createDir($path, $permissions);
		}

		/**
		 * return path's permissions
		 *
		 * @param string $path
		 *
		 * @return int
		 */
		static public function getPermission($path) {
			clearstatcache(true, $path);
			return octdec(substr(decoct(fileperms($path)), 1));
		}

		/**
		 * deletes a file or directory
		 *
		 * @param string $path
		 *
		 * @throws FileException
		 */
		static public function delete($path) {
			for ($i = 0; $i < 3; $i++) {
				try {
					if (is_file($path) || is_link($path)) {
						$func = DIRECTORY_SEPARATOR === '\\' && is_dir($path) ? 'rmdir' : 'unlink';
						/** @noinspection PhpUsageOfSilenceOperatorInspection */
						if (@$func($path) === false) {
							throw new FileException("Unable to delete [$path].");
						}
					} else if (is_dir($path)) {
						foreach (new \FilesystemIterator($path) as $item) {
							($realpath = $item->getRealPath()) ? static::delete($realpath) : null;
						}
						/** @noinspection PhpUsageOfSilenceOperatorInspection */
						if (@rmdir($path) === false) {
							throw new FileException("Unable to delete directory [$path].");
						}
					}
					break;
				} catch (FileException $exception) {
					usleep($i * 25);
				}
			}
			if (isset($exception)) {
				throw $exception;
			}
		}

		/**
		 * creates a directory
		 *
		 * @param string $dir
		 * @param int    $mode
		 *
		 * @throws FileException
		 */
		static public function createDir($dir, $mode = 0777) {
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			if (is_dir($dir) === false && @mkdir($dir, $mode, true) === false && is_dir($dir) === false) { // intentionally @; not atomic
				throw new DirectoryException("Unable to create directory [$dir].");
			}
		}

		/**
		 * copies a file or directory
		 *
		 * @param string        $source
		 * @param string        $destination
		 * @param callable|null $filter
		 *
		 * @throws FileException
		 */
		static public function copy(string $source, string $destination, callable $filter = null) {
			if (is_dir($source)) {
				self::copyDirectory($source, $destination, $filter);
				return;
			}
			try {
				if (file_exists($source) && (($sourceHandler = fopen($source, 'r')) === false || ($destinationHandler = fopen($destination, 'w')) === false || @stream_copy_to_stream($sourceHandler, $destinationHandler) === false)) {
					throw new FileException("Unable to copy file [$source] to [$destination].");
				}
			} finally {
				isset($sourceHandler) ? fclose($sourceHandler) : null;
				isset($destinationHandler) ? fclose($destinationHandler) : null;
			}
		}

		/**
		 * copy source directory tree to destination
		 *
		 * @param string        $source
		 * @param string        $destination
		 * @param callable|null $filter
		 *
		 * @throws FileException
		 */
		static public function copyDirectory(string $source, string $destination, callable $filter = null) {
			$source = self::normalize($source);
			$destination = self::normalize($destination);
			/** @var $item \SplFileInfo */
			foreach (new \RecursiveIteratorIterator($iterator = new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
				$path = self::normalize($destination . str_replace($source, '', self::normalize($item->getPath())));
				if ($filter && $filter($item, $path, $iterator) === false) {
					continue;
				}
				static::createDir($path);
				if ($item->isDir()) {
					continue;
				}
				self::copy((string)$item, $path . '/' . $item->getFilename());
			}
		}

		/**
		 * renames a file or directory
		 *
		 * @param string $name
		 * @param string $rename
		 * @param bool   $overwrite
		 *
		 * @throws FileException
		 */
		static public function rename(string $name, string $rename, bool $overwrite = true) {
			if ($overwrite === false && file_exists($rename)) {
				throw new FileException("File or directory [$rename] already exists.");
			} else if (file_exists($name) === false) {
				throw new FileException("File or directory [$name] not found.");
			}
			static::createDir(dirname($rename));
			static::delete($rename);
			/** @noinspection PhpUsageOfSilenceOperatorInspection */
			if (@rename($name, $rename) === false) {
				throw new FileException("Unable to rename file or directory [$name] to [$rename].");
			}
		}

		/**
		 * create url from the given file/path
		 *
		 * @param string $file
		 *
		 * @return IUrl
		 * @throws FileException
		 * @throws UrlException
		 */
		static public function url(string $file) {
			if (strpos($file, 'file:///') === false) {
				$file = 'file:///' . ltrim(self::realpath($file, false), '/');
			}
			return Url::create($file);
		}

		/**
		 * return realpath for the given path
		 *
		 * @param string $path
		 * @param bool   $required
		 *
		 * @return string
		 * @throws FileException
		 */
		static public function realpath($path, $required = true) {
			if (($real = realpath($path)) === false) {
				if ($required) {
					throw new RealPathException(sprintf('Cannot get real path from given string [%s].', $path));
				}
				$real = $path;
			}
			return self::normalize($real);
		}

		/**
		 * @param string $path
		 *
		 * @return string
		 */
		static public function normalize($path) {
			return rtrim(str_replace([
				'\\',
				'//',
			], [
				'/',
				'/',
			], $path), '/');
		}

		static public function size(string $path): float {
			$index = 0;
			$size = 1073741824;
			fseek($handle = fopen($path, 'r'), 0, SEEK_SET);
			while ($size > 1) {
				fseek($handle, $size, SEEK_CUR);
				if (fgetc($handle) === false) {
					fseek($handle, -$size, SEEK_CUR);
					$size = (int)($size / 2);
					continue;
				}
				fseek($handle, -1, SEEK_CUR);
				$index += $size;
			}
			while (fgetc($handle) !== false) {
				$index++;
			}
			return (float)$index;
		}
	}
