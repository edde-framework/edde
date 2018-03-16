<?php
	declare(strict_types=1);
	namespace Edde\Inject\Application;

	use Edde\Application\IApplication;

	trait Application {
		/**
		 * @var IApplication
		 */
		protected $application;

		/**
		 * @param IApplication $application
		 */
		public function lazyApplication(IApplication $application) {
			$this->application = $application;
		}
	}
