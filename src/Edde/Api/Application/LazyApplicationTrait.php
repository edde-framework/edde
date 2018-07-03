<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	trait LazyApplicationTrait {
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
