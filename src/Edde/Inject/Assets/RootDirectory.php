<?php
	declare(strict_types=1);
	namespace Edde\Inject\Assets;

	use Edde\Assets\IRootDirectory;

	trait RootDirectory {
		/**
		 * @var \Edde\Assets\IRootDirectory
		 */
		protected $rootDirectory;

		/**
		 * @param \Edde\Assets\IRootDirectory $rootDirectory
		 */
		public function lazyRootDirectory(\Edde\Assets\IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}
	}
