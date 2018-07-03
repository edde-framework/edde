<?php
	declare(strict_types=1);

	namespace Edde\Api\Store;

	trait LazyStoreDirectoryTrait {
		/**
		 * @var IStoreDirectory
		 */
		protected $storeDirectory;

		/**
		 * @param IStoreDirectory $storeDirectory
		 */
		public function lazyStoreDirectory(IStoreDirectory $storeDirectory) {
			$this->storeDirectory = $storeDirectory;
		}
	}
