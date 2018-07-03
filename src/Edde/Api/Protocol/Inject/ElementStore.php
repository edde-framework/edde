<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Inject;

	use Edde\Api\Protocol\IElementStore;

	trait ElementStore {
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
