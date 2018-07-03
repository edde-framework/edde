<?php
	declare(strict_types=1);

	namespace Edde\Api\Store;

	interface IStoreManager extends IStore {
		/**
		 * register a given store; if the name is not provided, get_class($store) should be used
		 *
		 * @param IStore $store
		 * @param string $name
		 *
		 * @return IStoreManager
		 */
		public function registerStore(IStore $store, string $name = null): IStoreManager;

		/**
		 * select a store with the given name as current
		 *
		 * @param string $name
		 *
		 * @return IStoreManager
		 */
		public function select(string $name): IStoreManager;

		/**
		 * when a Store is selected, it's pushed on stack; this method will restore previous store
		 *
		 * @return IStoreManager
		 */
		public function restore(): IStoreManager;

		/**
		 * return current store
		 *
		 * @return IStore
		 */
		public function getCurrentStore(): IStore;

		/**
		 * get the current store name
		 *
		 * @return string
		 */
		public function getCurrentName(): string;

		/**
		 * save value to the given store (select -> restore)
		 *
		 * @param string $name
		 * @param        $value
		 * @param string $store
		 *
		 * @return IStoreManager
		 */
		public function save(string $name, $value, string $store): IStoreManager;

		/**
		 * @param string $name
		 * @param string $store
		 * @param null   $default
		 *
		 * @return mixed
		 */
		public function load(string $name, string $store, $default = null);
	}
