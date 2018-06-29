<?php
	declare(strict_types=1);
	namespace Edde\File;

	use Edde\SimpleObject;
	use Edde\Url\Url;
	use function basename;
	use function dirname;
	use function file_exists;

	/**
	 * File class; this is just file. Simple good old classic file. Really.
	 */
	class File extends SimpleObject implements IFile {
		/** string */
		protected $file;
		/** @var IDirectory */
		protected $directory;
		/** @var resource */
		protected $handle;

		/**
		 * @param $file
		 */
		public function __construct($file) {
			$this->file = $file;
			$this->directory = new Directory(dirname($this->file));
		}

		/** @inheritdoc */
		public function getFile(): string {
			return $this->file;
		}

		/** @inheritdoc */
		public function getName(): string {
			return basename($this->file);
		}

		/** @inheritdoc */
		public function exists(): bool {
			return file_exists($this->file);
		}

		/** @inheritdoc */
		public function open(string $mode, bool $exclusive = false): IFile {
			if ($this->isOpen()) {
				if ($exclusive === false) {
					return $this;
				}
				throw new IoException(sprintf('Current file [%s] is already opened.', $this->file));
			}
			if (($this->handle = @fopen($path = $this->directory->getPath(), $mode)) === false) {
				throw new IoException(sprintf('Cannot open file [%s (%s)].', $path, $mode));
			}
			return $this;
		}

		/** @inheritdoc */
		public function isOpen(): bool {
			return $this->handle !== null;
		}

		/** @inheritdoc */
		public function read(int $length = null) {
			return ($length ? fgets($this->getHandle(), $length) : fgets($this->getHandle()));
		}

		/** @inheritdoc */
		public function write($write, int $length = null): IFile {
			if (($count = $length ? fwrite($this->getHandle(), $write, $length) : fwrite($this->getHandle(), $write)) !== ($length = strlen($write))) {
				throw new IoException(sprintf('Failed to write into file [%s]: expected %d bytes, %d has been written.', $this->directory->getPath(), $length, $count));
			}
			return $this;
		}

		/** @inheritdoc */
		public function rewind(): IFile {
			rewind($this->getHandle());
			return $this;
		}

		/** @inheritdoc */
		public function getHandle() {
			if ($this->isOpen() === false) {
				throw new IoException(sprintf('Current file [%s] is not opened or has been already closed.', $this->directory->getPath()));
			}
			return $this->handle;
		}

		/** @inheritdoc */
		public function close(): IFile {
			fflush($handle = $this->getHandle());
			fclose($handle);
			$this->handle = null;
			return $this;
		}

		/** @inheritdoc */
		public function delete(): IFile {
			if ($this->isOpen()) {
				$this->close();
			}
			unlink($this->directory->getPath());
			return $this;
		}

		/** @inheritdoc */
		public function save(string $content): IFile {
			if ($this->isOpen()) {
				throw new IoException(sprintf('Cannot write (save) content to already opened file [%s].', $this->directory->getPath()));
			}
			file_put_contents($this->file, $content);
			return $this;
		}

		/** @inheritdoc */
		public function rename(string $rename): IFile {
			if ($this->isOpen()) {
				throw new IoException(sprintf('Cannot rename already opened file [%s].', $this->directory->getPath()));
			}
			if (@rename($src = $this->file, $dst = ($this->directory->getPath() . '/' . $rename)) === false) {
				throw new IoException("Unable to rename file [$src] to [$dst].");
			}
			$this->file = $dst;
			return $this;
		}

		/** @inheritdoc */
		public function touch(): IFile {
			touch($this->directory->getPath());
			return $this;
		}

		/** @inheritdoc */
		public function getDirectory(): IDirectory {
			return $this->directory ?: $this->directory = new Directory(dirname($this->directory->getPath()));
		}

		/** @inheritdoc */
		public function getIterator() {
			$this->rewind();
			$count = 0;
			while ($line = $this->read()) {
				yield $count++ => $line;
			}
		}

		static public function create(string $file): IFile {
			return new static(Url::create('file:///' . ltrim($file, '/')));
		}
	}
