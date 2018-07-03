<?php
	declare(strict_types=1);

	namespace Edde\Api\Store;

	trait LazyStoreTrait {
		/**
		 * @var IStore
		 */
		protected $store;

		/**
		 * @param IStore $store
		 */
		public function lazyStore(IStore $store) {
			$this->store = $store;
		}
	}
