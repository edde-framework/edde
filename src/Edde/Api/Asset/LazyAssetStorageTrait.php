<?php
	declare(strict_types=1);

	namespace Edde\Api\Asset;

	/**
	 * Lazy asset storage dependency.
	 */
	trait LazyAssetStorageTrait {
		/**
		 * @var IAssetStorage
		 */
		protected $assetStorage;

		/**
		 * @param IAssetStorage $assetStorage
		 */
		public function lazyAssetStorage(IAssetStorage $assetStorage) {
			$this->assetStorage = $assetStorage;
		}
	}
