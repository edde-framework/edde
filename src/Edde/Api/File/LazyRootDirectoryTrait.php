<?php
	declare(strict_types = 1);

	namespace Edde\Api\File;

	/**
	 * LAzy dependency for a root directory.
	 */
	trait LazyRootDirectoryTrait {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		/**
		 * @param IRootDirectory $rootDirectory
		 */
		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}
	}
