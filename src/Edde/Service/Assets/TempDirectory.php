<?php
	declare(strict_types=1);
	namespace Edde\Service\Assets;

	use Edde\Assets\ITempDirectory;

	trait TempDirectory {
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		/**
		 * @param ITempDirectory $tempDirectory
		 */
		public function injectTempDirectory(ITempDirectory $tempDirectory) {
			$this->tempDirectory = $tempDirectory;
		}
	}
