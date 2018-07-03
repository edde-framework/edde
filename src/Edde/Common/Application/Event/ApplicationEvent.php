<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application\Event;

	use Edde\Api\Application\IApplication;
	use Edde\Common\Event\AbstractEvent;

	class ApplicationEvent extends AbstractEvent {
		/**
		 * @var IApplication
		 */
		protected $application;

		public function __construct(IApplication $application) {
			$this->application = $application;
		}

		/**
		 * @return IApplication
		 */
		public function getApplication(): IApplication {
			return $this->application;
		}
	}
