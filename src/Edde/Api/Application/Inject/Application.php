<?php
	declare(strict_types=1);

	namespace Edde\Api\Application\Inject;

	use Edde\Api\Application\IApplication;

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
