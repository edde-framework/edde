<?php
	declare(strict_types = 1);

	namespace Edde\Api;

	/**
	 * Edde assets directory lazy dependency.
	 */
	trait LazyAssetsDirectoryTrait {
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
