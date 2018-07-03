<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Cache;

	use Edde\Common\Cache\AbstractCacheStorage;

	/**
	 * Simple in-memory cache (per-request).
	 */
	class InMemoryCacheStorage extends AbstractCacheStorage {
		protected $storage;

		public function save(string $id, $save) {
			$this->use();
			return $this->storage[$id] = $save;
		}

		public function load($id) {
			$this->use();
			if (isset($this->storage[$id]) === false) {
				return null;
			}
			return $this->storage[$id];
		}

		public function invalidate() {
			$this->use();
			$this->storage = [];
			return $this;
		}
	}
