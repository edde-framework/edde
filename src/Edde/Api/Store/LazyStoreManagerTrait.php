<?php
	declare(strict_types=1);

	namespace Edde\Api\Store;

	trait LazyStoreManagerTrait {
		/**
		 * @var IStoreManager
		 */
		protected $storeManager;

		/**
		 * @param IStoreManager $storeManager
		 */
		public function lazyStoreManager(IStoreManager $storeManager) {
			$this->storeManager = $storeManager;
		}
	}
