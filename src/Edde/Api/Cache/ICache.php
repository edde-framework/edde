<?php
	declare(strict_types=1);

	namespace Edde\Api\Cache;

	/**
	 * Formal interface for a cache implementation.
	 */
	interface ICache {
		/**
		 * set/change cache namespace (this can be used for soft invalidation)
		 *
		 * @param string $namespace
		 *
		 * @return ICache
		 */
		public function setNamespace(string $namespace): ICache;

		/**
		 * save given value into the cache
		 *
		 * @param string $name
		 * @param mixed  $save must be serializable
		 *
		 * @return mixed
		 */
		public function save(string $name, $save);

		/**
		 * load value be the id - if the value doesn't exists, default is returned
		 *
		 * @param string     $name
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function load(string $name, $default = null);

		/**
		 * manual invalidation of whole cache
		 *
		 * @return ICache
		 */
		public function invalidate(): ICache;
	}
