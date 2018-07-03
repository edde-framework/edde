<?php
	declare(strict_types=1);

	namespace Edde\Api\Store;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Store is intelligent key-value implementation with support of "transactions" and locking.
	 *
	 * This class is intended to be simple and effective to use and persist simple pieces of data; it
	 * could be used for sessions, cache or just small pieces which is not necessary to save in database.
	 *
	 * The store itself is able somehow emulate database in really small applications.
	 */
	interface IStore extends IConfigurable {
		/**
		 * lock the given key or whole store; if block is set to false and lock cannot be ackquired, exception should
		 * be thrown
		 *
		 * @param string|null $name
		 * @param bool        $block
		 *
		 * @return IStore
		 */
		public function lock(string $name = null, bool $block = true): IStore;

		/**
		 * puts lock on the given name; if the lock is already there, function would block for the
		 * given timeout; if zero is provided, timeout is disabled
		 *
		 * @param string|null $name
		 * @param int         $timeout
		 *
		 * @return IStore
		 */
		public function block(string $name = null, int $timeout = null): IStore;

		/**
		 * unlock the given key or whole store
		 *
		 * @param string|null $name
		 *
		 * @return IStore
		 */
		public function unlock(string $name = null): IStore;

		/**
		 * if there is need to explicitly kill a lock created by another thread
		 *
		 * @param string|null $name
		 *
		 * @return IStore
		 */
		public function kill(string $name = null): IStore;

		/**
		 * is the store itself or the given key locked?
		 *
		 * @param string|null $name
		 *
		 * @return bool
		 */
		public function isLocked(string $name = null): bool;

		/**
		 * store a given value
		 *
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return IStore
		 */
		public function set(string $name, $value): IStore;

		/**
		 * exclusive set (block -> set -> unlock)
		 *
		 * @param string   $name
		 * @param mixed    $value
		 * @param int|null $timeout
		 *
		 * @return IStore
		 */
		public function sete(string $name, $value, int $timeout = null): IStore;

		/**
		 * take the value and append it using blocking approach; flow is (block -> set -> unlock)
		 *
		 * @param string   $name
		 * @param mixed    $value
		 * @param int|null $timeout
		 *
		 * @return IStore
		 */
		public function append(string $name, $value, int $timeout = null): IStore;

		/**
		 * is the given value present in the store?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function has(string $name): bool;

		/**
		 * get a data from the store
		 *
		 * @param string $name
		 * @param null   $default
		 *
		 * @return mixed
		 */
		public function get(string $name, $default = null);

		/**
		 * go through whole store and get all key -> value; this method should not work in common with arrays
		 *
		 * @return \Traversable
		 */
		public function iterate();

		/**
		 * remove the given key from store
		 *
		 * @param string $name
		 *
		 * @return IStore
		 */
		public function remove(string $name): IStore;

		/**
		 * get and remove the value from the store (block -> get -> remove -> unlock)
		 *
		 * @param string   $name
		 * @param null     $default
		 * @param int|null $timeout
		 *
		 * @return mixed
		 */
		public function pickup(string $name, $default = null, int $timeout = null);

		/**
		 * delete whole store (basically same as a database drop)
		 *
		 * @return IStore
		 */
		public function drop(): IStore;
	}
