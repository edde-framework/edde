<?php
	declare(strict_types=1);

	namespace Edde\Api\Log;

	/**
	 * Log directory lazy dependency.
	 */
	trait LazyLogDirectoryTrait {
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
