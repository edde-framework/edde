<?php
	declare(strict_types=1);
	namespace Edde\File;

	use Edde\Common\Resource\Resource;
	use Edde\Common\Url\Url;

	/**
	 * File class; this is just file. Simple good old classic file. Really.
	 */
	class File extends Resource implements IFile {
		/** @var IDirectory */
		protected $directory;
		/** @var resource */
		protected $handle;

		/** @inheritdoc */
		public function open(string $mode, bool $exclusive = false): IFile {
			if ($this->isOpen()) {
				if ($exclusive === false) {
					return $this;
				}
				throw new FileException(sprintf('Current file [%s] is already opened.', $this->getUrl()));
			}
			if (($this->handle = @fopen($path = $this->getPath(), $mode)) === false) {
				throw new FileException(sprintf('Cannot open file [%s (%s)].', $path, $mode));
			}
			return $this;
		}

		/** @inheritdoc */
		public function openForRead(bool $exclusive = false): IFile {
			$this->open('rb+', $exclusive);
			return $this;
		}

		/** @inheritdoc */
		public function openForWrite(bool $exclusive = false): IFile {
			$this->open('wb+', $exclusive);
			return $this;
		}

		/** @inheritdoc */
		public function openForAppend(bool $exclusive = false): IFile {
			$this->open('a', $exclusive);
			return $this;
		}

		/** @inheritdoc */
		public function isOpen(): bool {
			return $this->handle !== null;
		}

		/** @inheritdoc */
		public function read(int $length = null) {
			if (($line = ($length ? fgets($this->getHandle(), $length) : fgets($this->getHandle()))) === false) {
				$this->close();
			}
			return $line;
		}

		/** @inheritdoc */
		public function write($write, int $length = null): IFile {
			if ($this->isOpen() === false) {
				$this->openForWrite();
			}
			if (($count = $length ? fwrite($this->getHandle(), $write, $length) : fwrite($this->getHandle(), $write)) !== ($length = strlen($write))) {
				throw new FileException(sprintf('Failed to write into file [%s]: expected %d bytes, %d has been written.', $this->getPath(), $length, $count));
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
				throw new FileException(sprintf('Current file [%s] is not opened or has been already closed.', $this->getPath()));
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
			unlink($this->getPath());
			return $this;
		}

		/** @inheritdoc */
		public function save(string $content): IFile {
			if ($this->isOpen()) {
				throw new FileException(sprintf('Cannot write (save) content to already opened file [%s].', $this->getPath()));
			}
			$this->getDirectory()->create();
			file_put_contents($this->getPath(), $content);
			return $this;
		}

		/** @inheritdoc */
		public function rename(string $rename): IFile {
			if ($this->isOpen()) {
				throw new FileException(sprintf('Cannot rename already opened file [%s].', $this->getPath()));
			}
			if (@rename($src = $this->getPath(), $dst = ($this->getUrl()->getBasePath() . '/' . $rename)) === false) {
				throw new FileException("Unable to rename file or directory [$src] to [$dst].");
			}
			return $this;
		}

		/** @inheritdoc */
		public function lock(bool $exclusive = true, bool $block = true): IFile {
			if ($this->isOpen()) {
				throw new FileLockException(sprintf('File being lock must not be opened.'));
			}
			$exclusive ? $this->openForWrite() : $this->openForRead();
			if (flock($this->getHandle(), $exclusive ? (LOCK_EX | ($block ? 0 : LOCK_NB)) : LOCK_SH) === false) {
				throw new FileLockException(sprintf('Cannot execute lock on file [%s].', $this->getPath()));
			}
			return $this;
		}

		/** @inheritdoc */
		public function blockingLock(): IFile {
			return $this->lock(true, true);
		}

		/** @inheritdoc */
		public function nonBlockingLock(): IFile {
			return $this->lock(true, false);
		}

		/** @inheritdoc */
		public function unlock(): IFile {
			fflush($handle = $this->getHandle());
			flock($handle, LOCK_UN);
			return $this;
		}

		/** @inheritdoc */
		public function touch(): IFile {
			touch($this->getPath());
			return $this;
		}

		/** @inheritdoc */
		public function getDirectory(): IDirectory {
			return $this->directory ?: $this->directory = new Directory(dirname($this->getPath()));
		}

		/** @inheritdoc */
		public function getIterator() {
			if ($this->isOpen() === false) {
				$this->openForRead();
			}
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
