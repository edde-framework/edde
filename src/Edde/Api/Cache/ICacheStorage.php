<?php
	declare(strict_types=1);

	namespace Edde\Api\Cache;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Cache storage implementation.
	 */
	interface ICacheStorage extends IConfigurable {
		/**
		 * @param string $id
		 * @param mixed  $save must be serializable
		 *
		 * @return mixed returns input $save
		 */
		public function save(string $id, $save);

		/**
		 * @param string $id
		 *
		 * @return mixed
		 */
		public function load($id);

		/**
		 * invalidate whole cache storage
		 *
		 * @return $this
		 */
		public function invalidate();

		/**
		 * number of cache hits
		 *
		 * @return int
		 */
		public function getHitCount(): int;

		/**
		 * number of cache miss
		 *
		 * @return int
		 */
		public function getMissCount(): int;

		/**
		 * number of cache writes
		 *
		 * @return int
		 */
		public function getWriteCount(): int;
	}
