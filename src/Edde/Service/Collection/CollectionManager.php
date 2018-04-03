<?php
	declare(strict_types=1);
	namespace Edde\Service\Collection;

	use Edde\Collection\ICollectionManager;

	trait CollectionManager {
		/** @var ICollectionManager */
		protected $collectionManager;

		/**
		 * @param ICollectionManager $collectionManager
		 */
		public function injectCollectionManager(ICollectionManager $collectionManager): void {
			$this->collectionManager = $collectionManager;
		}
	}
