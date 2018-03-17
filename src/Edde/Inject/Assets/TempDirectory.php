<?php
	declare(strict_types=1);
	namespace Edde\Inject\Assets;

	use Edde\Assets\ITempDirectory;

	trait TempDirectory {
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		/**
		 * @param ITempDirectory $tempDirectory
		 */
		public function lazyTempDirectory(ITempDirectory $tempDirectory) {
			$this->tempDirectory = $tempDirectory;
		}
	}
