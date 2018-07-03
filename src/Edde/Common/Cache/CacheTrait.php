<?php
	declare(strict_types=1);

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

		protected function cache(): ICache {
			if ($this->cache === null) {
				$this->cacheManager->setup();
				$this->cache = $this->cacheManager->createCache(static::class);
			}
			return $this->cache;
		}
	}
