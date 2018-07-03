<?php
	declare(strict_types = 1);

	use Edde\Common\Cache\AbstractCacheStorage;

	class TestCacheStorage extends AbstractCacheStorage {
		private $cache;

		public function save(string $id, $save) {
			$this->cache[$id] = $save;
			return $save;
		}

		public function load($id) {
			return $this->cache[$id] ?? null;
		}

		public function invalidate() {
			$this->cache = [];
			return $this;
		}

		protected function prepare() {
		}
	}
