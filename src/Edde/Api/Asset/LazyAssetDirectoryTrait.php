<?php
	declare(strict_types=1);

	namespace Edde\Api\Asset;

	/**
	 * Lazy asset directory dependency.
	 */
	trait LazyAssetDirectoryTrait {
		/**
		 * @var IAssetDirectory
		 */
		protected $assetDirectory;

		/**
		 * @param IAssetDirectory $assetDirectory
		 */
		public function lazyAssetDirectory(IAssetDirectory $assetDirectory) {
			$this->assetDirectory = $assetDirectory;
		}
	}
