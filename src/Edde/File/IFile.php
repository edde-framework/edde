<?php
declare(strict_types=1);

namespace Edde\File;

use IteratorAggregate;

interface IFile extends IteratorAggregate {
    /**
     * return full path with filename
     *
     * @return string
     */
    public function getFile(): string;

    /**
     * return just filename
     *
     * @return string
     */
    public function getName(): string;

    /**
     * return directory of this file
     *
     * @return IDirectory
     */
    public function getDirectory(): IDirectory;

    /**
     * tells if file exists
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * create file handle; if the file is not available, exception should be thrown
     *
     * @param string $mode
     *
     * @return IFile
     *
     * @throws FileException
     */
    public function open(string $mode): IFile;

    /**
     * @return bool
     */
    public function isOpen(): bool;

    /**
     * read bunch of data
     *
     * @param int $length
     *
     * @return bool|string
     *
     * @throws FileException
     */
    public function read(int $length = null);

    /**
     * write bunch of data
     *
     * @param mixed $write
     * @param int   $length
     *
     * @return bool|int
     *
     * @throws FileException
     */
    public function write($write, int $length = null);

    /**
     * @return IFile
     *
     * @throws FileException
     */
    public function rewind(): IFile;

    /**
     * return file's resource; if it is not open, exception should be thrown
     *
     * @return resource
     *
     * @throws FileException
     */
    public function getHandle();

    /**
     * close the current file handle
     *
     * @return IFile
     *
     * @throws FileException
     */
    public function close(): IFile;

    /**
     * @return IFile
     *
     * @throws FileException
     */
    public function delete(): IFile;

    /**
     * rename a file (in current directory, this does NOT move a file)
     *
     * @param string $rename
     *
     * @return IFile
     *
     * @throws FileException
     */
    public function rename(string $rename): IFile;

    /**
     * override current file with the given content
     *
     * @param string $content
     *
     * @return IFile
     *
     * @throws FileException
     */
    public function save(string $content): IFile;

    /**
     * return whole content of a file; be careful as this involves memory limit
     *
     * @return string
     */
    public function load(): string;

    /**
     * only creates an empty file
     *
     * @return IFile
     *
     * @throws FileException
     */
    public function touch(): IFile;
}
