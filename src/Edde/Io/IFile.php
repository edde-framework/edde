<?php
	declare(strict_types=1);
	namespace Edde\Io;

	interface IFile extends IResource {
		/**
		 * create file handle; if the file is not available, exception should be thrown
		 *
		 * @param string $mode
		 * @param bool   $exclusive if the file is already opened, exception should be thrown
		 *
		 * @return IFile
		 *
		 * @throws IoException
		 */
		public function open(string $mode, bool $exclusive = false): IFile;

		/**
		 * @param bool $exclusive
		 *
		 * @return IFile
		 *
		 * @throws IoException
		 */
		public function openForRead(bool $exclusive = false): IFile;

		/**
		 * @param bool $exclusive
		 *
		 * @return IFile
		 *
		 * @throws IoException
		 */
		public function openForWrite(bool $exclusive = false): IFile;

		/**
		 * @param bool $exclusive
		 *
		 * @return IFile
		 *
		 * @throws IoException
		 */
		public function openForAppend(bool $exclusive = false): IFile;

		/**
		 * @return bool
		 */
		public function isOpen(): bool;

		/**
		 * read bunch of data
		 *
		 * @param int $length
		 *
		 * @return mixed
		 *
		 * @throws IoException
		 */
		public function read(int $length = null);

		/**
		 * write bunch of data
		 *
		 * @param mixed $write
		 * @param int   $length
		 *
		 * @return IFile
		 *
		 * @throws IoException
		 */
		public function write($write, int $length = null): IFile;

		/**
		 * @return IFile
		 *
		 * @throws IoException
		 */
		public function rewind(): IFile;

		/**
		 * return file's resource; if it is not open, exception should be thrown
		 *
		 * @return resource
		 *
		 * @throws IoException
		 */
		public function getHandle();

		/**
		 * close the current file handle
		 *
		 * @return IFile
		 *
		 * @throws IoException
		 */
		public function close(): IFile;

		/**
		 * @return IFile
		 *
		 * @throws IoException
		 */
		public function delete(): IFile;

		/**
		 * rename a file
		 *
		 * @param string $rename
		 *
		 * @return IFile
		 *
		 * @throws IoException
		 */
		public function rename(string $rename): IFile;

		/**
		 * override current file with the given content
		 *
		 * @param string $content
		 *
		 * @return IFile
		 *
		 * @throws IoException
		 */
		public function save(string $content): IFile;

		/**
		 * create a file and do an exclusive lock or lock an existing file; if lock cannot be acquired, exception should be thrown
		 *
		 * @param bool $exclusive
		 * @param bool $block
		 *
		 * @return IFile
		 *
		 * @throws FileLockException
		 * @throws IoException
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

		/**
		 * return directory of this file
		 *
		 * @return IDirectory
		 */
		public function getDirectory(): IDirectory;
	}
