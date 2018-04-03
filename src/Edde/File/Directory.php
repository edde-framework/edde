<?php
	declare(strict_types=1);
	namespace Edde\File;

	use Edde\Edde;
	use FilesystemIterator;
	use RecursiveDirectoryIterator;
	use RecursiveIteratorIterator;
	use SplFileInfo;

	/**
	 * Representation of directory on the filesystem.
	 */
	class Directory extends Edde implements IDirectory {
		/** @var string */
		protected $directory;

		/**
		 * @param string $directory
		 */
		public function __construct(string $directory) {
			$this->directory = $directory;
		}

		/** @inheritdoc */
		public function getFiles() {
			yield from (new RecursiveDirectoryIterator($this->directory, RecursiveDirectoryIterator::SKIP_DOTS));
		}

		/** @inheritdoc */
		public function getDirectories() {
			/** @var $path SplFileInfo */
			foreach (new RecursiveDirectoryIterator($this->directory, RecursiveDirectoryIterator::SKIP_DOTS) as $path) {
				if ($path->isDir()) {
					yield new self((string)$path);
				}
			}
		}

		/** @inheritdoc */
		public function normalize(): IDirectory {
			$this->directory = rtrim(str_replace([
				'\\',
				'//',
			], [
				'/',
				'/',
			], $this->directory), '/');
			return $this;
		}

		/** @inheritdoc */
		public function realpath(): IDirectory {
			$this->normalize();
			if (($path = realpath($this->directory)) === false) {
				throw new RealPathException(sprintf('Cannot get real path from given directory [%s].', $this->directory));
			}
			$this->directory = $path;
			return $this;
		}

		/** @inheritdoc */
		public function save(string $file, string $content): IFile {
			$file = $this->file($file);
			$file->save($content);
			return $file;
		}

		/** @inheritdoc */
		public function filename(string $file): string {
			return $this->getDirectory() . '/' . $file;
		}

		/** @inheritdoc */
		public function getDirectory(): string {
			return $this->directory;
		}

		/** @inheritdoc */
		public function getName(): string {
			return basename($this->directory);
		}

		/** @inheritdoc */
		public function file(string $file): IFile {
			return File::create($this->filename($file));
		}

		/** @inheritdoc */
		public function create(int $chmod = 0777): IDirectory {
			if (is_dir($this->directory) === false && @mkdir($this->directory, $chmod, true) && is_dir($this->directory) === false) {
				throw new DirectoryException(sprintf('Cannot create directory [%s].', $this->directory));
			}
			$this->realpath();
			return $this;
		}

		/** @inheritdoc */
		public function getPermission() {
			$this->realpath();
			clearstatcache(true, $this->directory);
			return octdec(substr(decoct(fileperms($this->directory)), 1));
		}

		/** @inheritdoc */
		public function purge(): IDirectory {
			$permissions = 0777;
			$this->realpath();
			if (file_exists($this->directory)) {
				$permissions = $this->getPermission();
			}
			$this->delete();
			$this->create($permissions);
			return $this;
		}

		/** @inheritdoc */
		public function delete(): IDirectory {
			try {
				$this->realpath();
				$path = $this->directory;
				for ($i = 0; $i < 3; $i++) {
					try {
						unset($exception);
						if (is_file($path) || is_link($path)) {
							$func = DIRECTORY_SEPARATOR === '\\' && is_dir($path) ? 'rmdir' : 'unlink';
							if (@$func($path) === false) {
								throw new DirectoryException("Unable to delete [$path].");
							}
						} else if (is_dir($path)) {
							foreach (new FilesystemIterator($path) as $item) {
								($realpath = $item->getRealPath()) ? (new Directory($realpath))->delete() : null;
							}
							if (@rmdir($path) === false) {
								throw new DirectoryException("Unable to delete directory [$path].");
							}
						}
						break;
					} catch (DirectoryException $exception) {
						sleep(1);
					}
				}
				if (isset($exception)) {
					throw $exception;
				}
			} catch (RealPathException $_) {
			}
			return $this;
		}

		/** @inheritdoc */
		public function exists(): bool {
			return is_dir($this->directory);
		}

		/** @inheritdoc */
		public function directory(string $directory, string $class = null): IDirectory {
			$class = $class ?: Directory::class;
			return new $class($this->directory . '/' . $directory);
		}

		/** @inheritdoc */
		public function parent(): IDirectory {
			return new Directory($this->getDirectory() . '/..');
		}

		/** @inheritdoc */
		public function files() {
			return $this->getIterator();
		}

		/** @inheritdoc */
		public function getIterator() {
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->directory, RecursiveDirectoryIterator::SKIP_DOTS)) as $splFileInfo) {
				yield File::create((string)$splFileInfo);
			}
		}

		public function __toString() {
			return $this->getDirectory();
		}
	}
