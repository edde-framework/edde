<?php
	declare(strict_types=1);
	namespace Edde\Inject\Assets;

	use Edde\Api\Assets\IAssetsDirectory;

	trait AssetsDirectory {
		/**
		 * @var IAssetsDirectory
		 */
		protected $assetsDirectory;

		/**
		 * @param IAssetsDirectory $assetsDirectory
		 */
		public function lazyAssetsDirectory(IAssetsDirectory $assetsDirectory) {
			$this->assetsDirectory = $assetsDirectory;
		}
	}
