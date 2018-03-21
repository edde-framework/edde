<?php
	declare(strict_types=1);
	namespace Edde\Service\Assets;

	use Edde\Assets\ILogDirectory;

	/**
	 * Log directory lazy dependency.
	 */
	trait LogDirectory {
		/**
		 * @var ILogDirectory
		 */
		protected $logDirectory;

		/**
		 * @param ILogDirectory $logDirectory
		 */
		public function lazyLogDirectory(ILogDirectory $logDirectory) {
			$this->logDirectory = $logDirectory;
		}
	}
