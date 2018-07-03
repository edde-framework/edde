<?php
	declare(strict_types = 1);

	namespace Edde\Api\Cache;

	/**
	 * Lazy cache storage dependency.
	 */
	trait LazyCacheStorageTrait {
		/**
		 * @var ICacheStorage
		 */
		protected $cacheStorage;

		/**
		 * @param ICacheStorage $cacheStorage
		 */
		public function lazyCacheStorage(ICacheStorage $cacheStorage) {
			$this->cacheStorage = $cacheStorage;
		}
	}
