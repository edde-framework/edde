<?php
	declare(strict_types=1);
	namespace Edde\Inject\Assets;

	use Edde\Assets\ILogDirectory;

	/**
	 * Log directory lazy dependency.
	 */
	trait LogDirectory {
		/**
		 * @var \Edde\Assets\ILogDirectory
		 */
		protected $logDirectory;

		/**
		 * @param \Edde\Assets\ILogDirectory $logDirectory
		 */
		public function lazyLogDirectory(ILogDirectory $logDirectory) {
			$this->logDirectory = $logDirectory;
		}
	}
