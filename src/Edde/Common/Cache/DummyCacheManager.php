<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Ext\Cache\DevNullCacheStorage;

	class DummyCacheManager extends CacheManager {
		public function __construct() {
			parent::__construct(new DevNullCacheStorage());
		}

		public function registerCacheStorage(string $namespace, ICacheStorage $cacheStorage): ICacheManager {
			return $this;
		}
	}
