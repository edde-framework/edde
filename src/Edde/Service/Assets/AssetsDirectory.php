<?php
	declare(strict_types=1);
	namespace Edde\Service\Assets;

	use Edde\Assets\IAssetsDirectory;

	trait AssetsDirectory {
		/**
		 * @var IAssetsDirectory
		 */
		protected $assetsDirectory;

		/**
		 * @param IAssetsDirectory $assetsDirectory
		 */
		public function injectAssetsDirectory(IAssetsDirectory $assetsDirectory) {
			$this->assetsDirectory = $assetsDirectory;
		}
	}
