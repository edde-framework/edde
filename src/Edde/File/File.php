<?php
declare(strict_types=1);

namespace Edde\File;

use Edde\SimpleObject;
use function basename;
use function dirname;
use function file_exists;
use function file_get_contents;
use function sprintf;

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
    /** @var string */
    protected $mode;

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
    public function getDirectory(): IDirectory {
        return $this->directory;
    }

    /** @inheritdoc */
    public function exists(): bool {
        return file_exists($this->file);
    }

    /** @inheritdoc */
    public function open(string $mode): IFile {
        if ($this->isOpen()) {
            if ($mode === $this->mode) {
                return $this;
            }
            throw new FileException(sprintf('Current file [%s] is already opened in different mode [%s].', $this->file, $this->mode));
        }
        if (($this->handle = @fopen($this->file, $mode)) === false) {
            throw new FileException(sprintf('Cannot open file [%s (%s)].', $this->file, $mode));
        }
        $this->mode = $mode;
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
    public function write($write, int $length = null) {
        return $length ? fwrite($this->getHandle(), $write, $length) : fwrite($this->getHandle(), $write);
    }

    /** @inheritdoc */
    public function rewind(): IFile {
        rewind($this->getHandle());
        return $this;
    }

    /** @inheritdoc */
    public function getHandle() {
        if ($this->isOpen() === false) {
            throw new FileException(sprintf('Current file [%s] is not opened or has been already closed.', $this->file));
        }
        return $this->handle;
    }

    /** @inheritdoc */
    public function close(): IFile {
        fflush($handle = $this->getHandle());
        fclose($handle);
        $this->handle = null;
        $this->mode = null;
        return $this;
    }

    /** @inheritdoc */
    public function delete(): IFile {
        if ($this->isOpen()) {
            throw new FileException(sprintf('Cannot delete opened [%s] file [%s].', $this->mode, $this->file));
        }
        unlink($this->file);
        return $this;
    }

    /** @inheritdoc */
    public function rename(string $rename): IFile {
        if ($this->isOpen()) {
            throw new FileException(sprintf('Cannot rename opened [%s] file [%s] to [%s].', $this->mode, $this->file, $rename));
        }
        if (@rename($src = $this->file, $dst = ($this->directory->getPath() . '/' . $rename)) === false) {
            throw new FileException("Unable to rename file [$src] to [$dst].");
        }
        $this->file = $dst;
        return $this;
    }

    /** @inheritdoc */
    public function save(string $content): IFile {
        if ($this->isOpen()) {
            throw new FileException(sprintf('Cannot save content to already opened [%s] file [%s].', $this->mode, $this->file));
        }
        file_put_contents($this->file, $content);
        return $this;
    }

    /** @inheritdoc */
    public function load(): string {
        return file_get_contents($this->file);
    }

    /** @inheritdoc */
    public function touch(): IFile {
        if ($this->isOpen()) {
            throw new FileException(sprintf('Cannot touch already opened [%s] file [%s].', $this->mode, $this->file));
        }
        if (@touch($this->file) === false) {
            throw new FileException(sprintf('Cannot touch file [%s].', $this->file));
        }
        return $this;
    }

    /** @inheritdoc */
    public function getIterator() {
        $this->rewind();
        $count = 0;
        while ($line = $this->read()) {
            yield $count++ => $line;
        }
    }
}
