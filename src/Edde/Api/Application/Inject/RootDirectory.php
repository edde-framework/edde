<?php
	declare(strict_types=1);
	namespace Edde\Api\Application\Inject;

		use Edde\Api\Application\IRootDirectory;

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
