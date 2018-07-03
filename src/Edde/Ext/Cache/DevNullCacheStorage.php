<?php
	declare(strict_types=1);

	namespace Edde\Ext\Cache;

	use Edde\Common\Cache\AbstractCacheStorage;

	/**
	 * If caching may be off use this storage.
	 */
	class DevNullCacheStorage extends AbstractCacheStorage {
		public function save(string $id, $save) {
			return $save;
		}

		public function load($id) {
		}

		public function invalidate() {
			return $this;
		}
	}
