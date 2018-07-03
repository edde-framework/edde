<?php
	declare(strict_types = 1);

	namespace Edde\Common\RuntimeTest;

	use Edde\Common\Cache\AbstractCacheStorage;

	class DummyCacheStorage extends AbstractCacheStorage {
		public function save(string $id, $save) {
		}

		public function load($id) {
		}

		public function invalidate() {
		}

		protected function prepare() {
		}
	}
