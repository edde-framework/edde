<?php
	declare(strict_types=1);
	namespace Edde\File;

	use Edde\Edde;
	use FilesystemIterator;
	use RecursiveDirectoryIterator;
	use RecursiveIteratorIterator;
	use SplFileInfo;
	use function dirname;
	use function is_dir;
	use function sprintf;
	use function unlink;

	/**
	 * Representation of directory on the filesystem.
	 */
	class Directory extends Edde implements IDirectory {
		/** @var string */
		protected $directory;
		/** @var IDirectory */
		protected $parent;

		/**
		 * @param string $directory
		 */
		public function __construct(string $directory) {
			$this->directory = $directory;
		}

		/** @inheritdoc */
		public function getPath(): string {
			return $this->directory;
		}

		/** @inheritdoc */
		public function getName(): string {
			return basename($this->directory);
		}

		/** @inheritdoc */
		public function getFiles() {
			/** @var $splFileInfo SplFileInfo */
			foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->directory, RecursiveDirectoryIterator::SKIP_DOTS)) as $splFileInfo) {
				if ($splFileInfo->isFile()) {
					yield $splFileInfo;
				}
			}
		}

		/** @inheritdoc */
		public function save(string $file, string $content): IFile {
			$file = $this->file($file);
			$file->save($content);
			return $file;
		}

		/** @inheritdoc */
		public function filename(string $file): string {
			return $this->directory . '/' . $file;
		}

		/** @inheritdoc */
		public function file(string $file): IFile {
			return new File($this->filename($file));
		}

		/** @inheritdoc */
		public function create(int $chmod = 0777): IDirectory {
			if (@mkdir($this->directory, $chmod, true) === false) {
				throw new FileException(sprintf('Cannot create directory [%s] with [%o].', $this->directory, $chmod));
			}
			return $this;
		}

		/** @inheritdoc */
		public function getPermission(): int {
			clearstatcache(true, $this->directory);
			return octdec(substr(decoct(fileperms($this->directory)), 1));
		}

		/** @inheritdoc */
		public function purge(): IDirectory {
			$permissions = 0777;
			if (file_exists($this->directory)) {
				$permissions = $this->getPermission();
			}
			try {
				$this->delete();
			} catch (FileException $_) {
			}
			$this->create($permissions);
			return $this;
		}

		/** @inheritdoc */
		public function delete(): IDirectory {
			if (is_dir($this->directory) === false) {
				throw new FileException(sprintf('Directory [%s] is not directory or does not exists.', $this->directory));
			}
			/** @var $splFileInfo SplFileInfo */
			foreach (new FilesystemIterator($this->directory) as $splFileInfo) {
				$path = $splFileInfo->getRealPath();
				if ($splFileInfo->isFile()) {
					unlink($path);
					continue;
				}
				(new self($path))->delete();
			}
			@rmdir($this->directory);
			return $this;
		}

		/** @inheritdoc */
		public function exists(): bool {
			return is_dir($this->directory);
		}

		/** @inheritdoc */
		public function directory(string $directory): IDirectory {
			return new self($this->directory . '/' . $directory);
		}

		/** @inheritdoc */
		public function parent(): IDirectory {
			return $this->parent ?: $this->parent = new self(dirname($this->directory));
		}

		/** @inheritdoc */
		public function getIterator() {
			/** @var $splFileInfo SplFileInfo */
			foreach ($this->getFiles() as $splFileInfo) {
				yield new File($splFileInfo->getRealPath());
			}
		}
	}
