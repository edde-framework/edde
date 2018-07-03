<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	/**
	 * Lazy crate cache dependency.
	 */
	trait LazyCrateDirectoryTrait {
		/**
		 * @var ICrateDirectory
		 */
		protected $crateDirectory;

		/**
		 * @param ICrateDirectory $crateDirectory
		 */
		public function lazyCrateDirectory(ICrateDirectory $crateDirectory) {
			$this->crateDirectory = $crateDirectory;
		}
	}
