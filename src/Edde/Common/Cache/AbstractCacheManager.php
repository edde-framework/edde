<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheManager;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\Deffered\DefferedTrait;

	/**
	 * Common stuff for a cache cache implementation.
	 */
	abstract class AbstractCacheManager extends AbstractCache implements ICacheManager {
		use DefferedTrait;
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
		public function cache(string $namespace = null, ICacheStorage $cacheStorage = null): ICache {
			$this->use();
			return (new Cache($cacheStorage ?: ($this->cacheStorageList[$namespace] ?? $this->cacheStorage)))->setNamespace($this->namespace . $namespace);
		}
	}
