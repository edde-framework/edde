<?php
	declare(strict_types = 1);

	namespace Edde\Api\Cache;

	trait LazyCacheTrait {
		/**
		 * @var ICache
		 */
		protected $cache;

		/**
		 * @param ICache $cache
		 */
		public function lazyCache(ICache $cache) {
			$this->cache = $cache;
		}
	}
