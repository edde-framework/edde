<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	trait LazyElementStoreTrait {
		/**
		 * @var IElementStore
		 */
		protected $elementStore;

		/**
		 * @param IElementStore $elementStore
		 */
		public function lazyElementStore(IElementStore $elementStore) {
			$this->elementStore = $elementStore;
		}
	}
