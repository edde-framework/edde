<?php
	declare(strict_types=1);
	namespace Edde\Inject\Assets;

	use Edde\Api\Assets\IRootDirectory;

	trait RootDirectory {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		/**
		 * @param IRootDirectory $rootDirectory
		 */
		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}
	}
