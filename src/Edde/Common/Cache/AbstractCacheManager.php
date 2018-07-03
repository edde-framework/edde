<?php
	declare(strict_types=1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\Config\ConfigurableTrait;

	/**
	 * Common stuff for a cache cache implementation.
	 */
	abstract class AbstractCacheManager extends AbstractCache implements ICacheManager {
		use ConfigurableTrait;
		/**
		 * @var ICacheStorage[]
		 */
		protected $cacheStorageList = [];

		/**
		 * @inheritdoc
		 */
		public function registerCacheStorage(string $namespace, ICacheStorage $cacheStorage): ICacheManager {
			$this->cacheStorageList[$namespace] = $cacheStorage;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function invalidate(): ICache {
			parent::invalidate();
			foreach ($this->cacheStorageList as $cacheStorage) {
				$cacheStorage->invalidate();
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function createCache(string $namespace = null, ICacheStorage $cacheStorage = null): ICache {
			$cacheStorage = $cacheStorage ?: ($this->cacheStorageList[$namespace] ?? $this->cacheStorage);
			$cacheStorage->setup();
			return (new Cache($cacheStorage))->setNamespace($this->namespace . $namespace);
		}
	}
