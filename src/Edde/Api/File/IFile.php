<?php
	declare(strict_types=1);

	namespace Edde\Api\File;

	use Edde\Api\Resource\IResource;

	interface IFile extends IResource {
		/**
		 * enable autoclose after file is read
		 *
		 * @param bool|true $autoClose
		 *
		 * @return IFile
		 */
		public function setAutoClose(bool $autoClose = true): IFile;

		/**
		 * @return string
		 */
		public function getPath(): string;

		/**
		 * @return string|null
		 */
		public function getExtension();

		/**
		 * return directory of this file
		 *
		 * @return IDirectory
		 */
		public function getDirectory(): IDirectory;

		/**
		 * @return bool
		 */
		public function isAutoClose(): bool;

		/**
		 * rename a file
		 *
		 * @param string $rename
		 *
		 * @return IFile
		 */
		public function rename(string $rename): IFile;

		/**
		 * create file handle; if the file is not availble, exceptio nshould be thrown
		 *
		 * @param string $mode
		 * @param bool   $exclusive if the file is already opened, exception should be thrown
		 *
		 * @return IFile
		 */
		public function open(string $mode, bool $exclusive = false): IFile;

		/**
		 * @param bool $exclusive
		 *
		 * @return IFile
		 */
		public function openForRead(bool $exclusive = false): IFile;

		/**
		 * @param bool $exclusive
		 *
		 * @return IFile
		 */
		public function openForWrite(bool $exclusive = false): IFile;

		/**
		 * @param bool $exclusive
		 *
		 * @return IFile
		 */
		public function openForAppend(bool $exclusive = false): IFile;

		/**
		 * @return bool
		 */
		public function isOpen(): bool;

		/**
		 * return file's resource; if it is not open, exception should be thrown
		 *
		 * @return resource
		 */
		public function getHandle();

		/**
		 * close the current file handle
		 *
		 * @return IFile
		 */
		public function close(): IFile;

		/**
		 * read bunch of data
		 *
		 * @param int $length
		 *
		 * @return mixed
		 */
		public function read(int $length = null);

		/**
		 * write bunch of data
		 *
		 * @param mixed $write
		 * @param int   $length
		 *
		 * @return IFile
		 */
		public function write($write, int $length = null): IFile;

		/**
		 * override current file with the given content
		 *
		 * @param string $content
		 *
		 * @return IFile
		 */
		public function save(string $content): IFile;

		/**
		 * @return IFile
		 */
		public function rewind(): IFile;

		/**
		 * @return IFile
		 */
		public function delete(): IFile;

		/**
		 * return a file size
		 *
		 * @return float
		 */
		public function getSize(): float;

		/**
		 * run regexp against file path
		 *
		 * @param string $match
		 * @param bool   $filename
		 *
		 * @return mixed
		 */
		public function match(string $match, bool $filename = true);

		/**
		 * create a file and do an exclusive lock or lock an existing file; if lock cannot be acquired, exception should be thrown
		 *
		 * @param bool $exclusive
		 * @param bool $block
		 *
		 * @return IFile
		 */
		public function lock(bool $exclusive = true, bool $block = true): IFile;

		/**
		 * blocking lock is by default exclusive
		 *
		 * @return IFile
		 */
		public function blockingLock(): IFile;

		/**
		 * non blocking lock is be default exclusive
		 *
		 * @return IFile
		 */
		public function nonBlockingLock(): IFile;

		/**
		 * unlock the file or throw an exception if file is not locked
		 *
		 * @return IFile
		 */
		public function unlock(): IFile;

		/**
		 * only creates an empty file
		 *
		 * @return IFile
		 */
		public function touch(): IFile;
	}
