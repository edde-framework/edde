<?php
	namespace Edde\Api\Assets\Inject;

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