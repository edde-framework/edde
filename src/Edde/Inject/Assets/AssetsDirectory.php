<?php
	declare(strict_types=1);
	namespace Edde\Inject\Assets;

	use Edde\Assets\IAssetsDirectory;

	trait AssetsDirectory {
		/**
		 * @var \Edde\Assets\IAssetsDirectory
		 */
		protected $assetsDirectory;

		/**
		 * @param \Edde\Assets\IAssetsDirectory $assetsDirectory
		 */
		public function lazyAssetsDirectory(\Edde\Assets\IAssetsDirectory $assetsDirectory) {
			$this->assetsDirectory = $assetsDirectory;
		}
	}
