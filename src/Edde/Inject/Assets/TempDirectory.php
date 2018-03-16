<?php
	declare(strict_types=1);
	namespace Edde\Inject\Assets;

	use Edde\Assets\ITempDirectory;

	trait TempDirectory {
		/**
		 * @var \Edde\Assets\ITempDirectory
		 */
		protected $tempDirectory;

		/**
		 * @param \Edde\Assets\ITempDirectory $tempDirectory
		 */
		public function lazyTempDirectory(\Edde\Assets\ITempDirectory $tempDirectory) {
			$this->tempDirectory = $tempDirectory;
		}
	}
