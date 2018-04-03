<?php
	declare(strict_types=1);
	namespace Edde\Service\Application;

	use Edde\Application\IApplication;

	trait Application {
		/**
		 * @var IApplication
		 */
		protected $application;

		/**
		 * @param IApplication $application
		 */
		public function injectApplication(IApplication $application) {
			$this->application = $application;
		}
	}
