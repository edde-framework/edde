<?php
	declare(strict_types = 1);

	namespace Edde\Api\Session;

	trait LazySessionDirectoryTrait {
		/**
		 * @var ISessionDirectory
		 */
		protected $sessionDirectory;

		/**
		 * @param ISessionDirectory $sessionDirectory
		 */
		public function lazySessionDirectory(ISessionDirectory $sessionDirectory) {
			$this->sessionDirectory = $sessionDirectory;
		}
	}
