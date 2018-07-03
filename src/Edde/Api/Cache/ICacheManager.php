<?php
	declare(strict_types = 1);

	namespace Edde\Api\Cache;

	use Edde\Api\Deffered\IDeffered;

	interface ICacheManager extends ICache, IDeffered {
		/**
		 * register cache storage to a namespace, so if cache will be created with same namespace, the given cache storage will be used
		 *
		 * @param string $namespace
		 * @param ICacheStorage $cacheStorage
		 *
		 * @return ICacheManager
		 */
		public function registerCacheStorage(string $namespace, ICacheStorage $cacheStorage): ICacheManager;

		/**
		 * create a new cache
		 *
		 * @param string|null $namespace
		 * @param ICacheStorage $cacheStorage
		 *
		 * @return ICache
		 */
		public function cache(string $namespace = null, ICacheStorage $cacheStorage = null): ICache;
	}
