<?php
	declare(strict_types=1);

	namespace Edde\Api\Cache;

	/**
	 * Lazy cache cache dependency.
	 */
	trait LazyCacheManagerTrait {
		/**
		 * @var ICacheManager
		 */
		protected $cacheManager;

		/**
		 * @param ICacheManager $cacheManager
		 */
		public function lazyCacheFactory(ICacheManager $cacheManager) {
			$this->cacheManager = $cacheManager;
		}
	}
