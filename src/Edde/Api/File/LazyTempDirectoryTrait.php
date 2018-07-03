<?php
	declare(strict_types = 1);

	namespace Edde\Api\File;

	/**
	 * LAzy dependency for a temp directory.
	 */
	trait LazyTempDirectoryTrait {
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		/**
		 * @param ITempDirectory $tempDirectory
		 */
		public function lazyTempDirectory(ITempDirectory $tempDirectory) {
			$this->tempDirectory = $tempDirectory;
		}
	}
