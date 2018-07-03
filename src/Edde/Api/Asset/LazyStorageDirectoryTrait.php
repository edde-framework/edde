<?php
	declare(strict_types = 1);

	namespace Edde\Api\Asset;

	/**
	 * Lazy storage directory dependency.
	 */
	trait LazyStorageDirectoryTrait {
		/**
		 * @var IStorageDirectory
		 */
		protected $storageDirectory;

		/**
		 * @param IStorageDirectory $storageDirectory
		 */
		public function lazyStorageDirectory(IStorageDirectory $storageDirectory) {
			$this->storageDirectory = $storageDirectory;
		}
	}
