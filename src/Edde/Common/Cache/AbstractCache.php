<?php
	declare(strict_types=1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheStorage;
	use Edde\Common\Object;

	abstract class AbstractCache extends Object implements ICache {
		/**
		 * @var ICacheStorage
		 */
		protected $cacheStorage;
		/**
		 * @var string
		 */
		protected $namespace;

		/**
		 * Why is women’s soccer so rare?
		 *
		 * It’s quite hard to find enough women willing to wear the same outfit.
		 *
		 * @param ICacheStorage $cacheStorage
		 */
		public function __construct(ICacheStorage $cacheStorage) {
			$this->cacheStorage = $cacheStorage;
		}

		/**
		 * @inheritdoc
		 */
		public function setNamespace(string $namespace): ICache {
			$this->namespace = $namespace;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function load(string $name, $default = null) {
			$this->cacheStorage->setup();
			if (($value = $this->cacheStorage->load($this->cacheId($name))) === null) {
				return is_callable($default) ? call_user_func($default) : $default;
			}
			return $value;
		}

		/**
		 * @inheritdoc
		 */
		public function save(string $name, $source) {
			$this->cacheStorage->setup();
			$this->cacheStorage->save($this->cacheId($name), $source);
			return $source;
		}

		/**
		 * @inheritdoc
		 */
		public function invalidate(): ICache {
			$this->cacheStorage->setup();
			$this->cacheStorage->invalidate();
			return $this;
		}

		/**
		 * generate cache id
		 *
		 * @param string $name
		 *
		 * @return string
		 */
		protected function cacheId(string $name): string {
			return sha1($this->namespace . '/' . $name);
		}
	}
