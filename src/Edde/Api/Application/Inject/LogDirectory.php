<?php
	declare(strict_types=1);
	namespace Edde\Api\Application\Inject;

	use Edde\Api\Application\ILogDirectory;

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
