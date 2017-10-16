<?php
	namespace Edde\Api\Application\Inject;

		use Edde\Api\Application\ITempDirectory;

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
