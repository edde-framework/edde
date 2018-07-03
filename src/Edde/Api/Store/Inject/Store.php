<?php
	declare(strict_types=1);

	namespace Edde\Api\Store\Inject;

	use Edde\Api\Store\IStore;

	trait Store {
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
