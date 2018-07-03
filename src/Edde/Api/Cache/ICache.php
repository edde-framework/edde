<?php
	declare(strict_types = 1);

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
		 * cache method result
		 *
		 * @param string $name
		 * @param callable $callback
		 * @param array ...$parameterList
		 *
		 * @return mixed
		 */
		public function callback(string $name, callable $callback, ...$parameterList);

		/**
		 * save given value into the cache
		 *
		 * @param string $id
		 * @param mixed $save must be serializable (neonable, jsonable, serializable, ...)
		 *
		 * @return mixed
		 */
		public function save(string $id, $save);

		/**
		 * load value be the id - if the value doesn't exists, default is returned
		 *
		 * @param string $id
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function load(string $id, $default = null);

		/**
		 * manual invalidation of whole cache
		 *
		 * @return ICache
		 */
		public function invalidate(): ICache;
	}
