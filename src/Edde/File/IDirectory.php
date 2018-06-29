<?php
	declare(strict_types=1);
	namespace Edde\File;

	use IteratorAggregate;
	use SplFileInfo;

	interface IDirectory extends IteratorAggregate {
		/**
		 * return string path of this directory (can be non-existent)
		 *
		 * @return string
		 */
		public function getPath(): string;

		/**
		 * return directory name
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * return iterator over file list in the current directory
		 *
		 * @return SplFileInfo[]
		 */
		public function getFiles();

		/**
		 * create a file with the given name in this directory
		 *
		 * @param string $file
		 * @param string $content
		 *
		 * @return IFile
		 *
		 * @throws FileException
		 */
		public function save(string $file, string $content): IFile;

		/**
		 * create filename (shortcut for $this->getDirectory.'\\'.$file)
		 *
		 * @param string $file
		 *
		 * @return string
		 */
		public function filename(string $file): string;

		/**
		 * return a File object
		 *
		 * @param string $file
		 *
		 * @return IFile
		 */
		public function file(string $file): IFile;

		/**
		 * create all directories until the current one
		 *
		 * @param int $chmod
		 *
		 * @return IDirectory
		 *
		 * @throws FileException
		 */
		public function create(int $chmod = 0777): IDirectory;

		/**
		 * @return int
		 *
		 * @throws FileException
		 */
		public function getPermission(): int;

		/**
		 * recreate directory in place effectively clean all it's contents
		 *
		 * @return IDirectory
		 *
		 * @throws FileException
		 */
		public function purge(): IDirectory;

		/**
		 * physically remove the directory
		 *
		 * @return IDirectory
		 *
		 * @throws FileException
		 */
		public function delete(): IDirectory;

		/**
		 * @return bool
		 */
		public function exists(): bool;

		/**
		 * return directory based on a current path
		 *
		 * @param string $directory
		 *
		 * @return IDirectory
		 */
		public function directory(string $directory): IDirectory;

		/**
		 * return parent directory
		 *
		 * @return IDirectory
		 */
		public function parent(): IDirectory;

		/**
		 * @return IFile[]
		 */
		public function getIterator();
	}
