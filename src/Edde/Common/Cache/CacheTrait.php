<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\LazyCacheManagerTrait;

	/**
	 * This trait is shorthand for creating cache to a supported class (it must be created through container).
	 */
	trait CacheTrait {
		use LazyCacheManagerTrait;
		/**
		 * @var ICache
		 */
		protected $cache;

		protected function cache() {
			$this->lazy('cache', function () {
				return $this->cacheManager->cache(static::class);
			});
		}
	}
