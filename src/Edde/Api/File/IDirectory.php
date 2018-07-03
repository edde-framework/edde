<?php
	declare(strict_types=1);

	namespace Edde\Api\File;

	use IteratorAggregate;

	interface IDirectory extends IteratorAggregate {
		/**
		 * return string path of this directory (can be non-existent)
		 *
		 * @return string
		 */
		public function getDirectory(): string;

		/**
		 * return directory name
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * return iterator over file list in the current directory
		 *
		 * @return string[]
		 */
		public function getFileList();

		/**
		 * @return IDirectory[]
		 */
		public function getDirectoryList();

		/**
		 * normalize directory path
		 *
		 * @return IDirectory
		 */
		public function normalize(): IDirectory;

		/**
		 * execute realpath on directory to check if everything is correct (directory must exists)
		 *
		 * @return IDirectory
		 */
		public function realpath(): IDirectory;

		/**
		 * create a file with the given name in this directory
		 *
		 * @param string $file
		 * @param string $content
		 *
		 * @return IFile
		 */
		public function save(string $file, string $content): IFile;

		/**
		 * get contents of a given file (in this directory)
		 *
		 * @param string $file
		 *
		 * @return string
		 */
		public function get(string $file): string;

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
		 * @return IDirectory
		 */
		public function create(): IDirectory;

		/**
		 * recreate directory in place effectively clean all it's contents
		 *
		 * @return IDirectory
		 */
		public function purge(): IDirectory;

		/**
		 * physically remove the directory
		 *
		 * @return IDirectory
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
		 * @param string $class directory class; sometimes it's useful return non default IDirectory interface
		 *
		 * @return IDirectory
		 */
		public function directory(string $directory, string $class = null): IDirectory;

		/**
		 * return parent directory
		 *
		 * @return IDirectory
		 */
		public function parent(): IDirectory;

		/**
		 * a bit more talkative iterator
		 *
		 * @return IFile[]
		 */
		public function files();

		/**
		 * @return IFile[]
		 */
		public function getIterator();
	}
