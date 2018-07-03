<?php
	declare(strict_types=1);

	namespace Edde\Api\Store\Inject;

	use Edde\Api\Store\IStoreDirectory;

	trait StoreDirectory {
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
