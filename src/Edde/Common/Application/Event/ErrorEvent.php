<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application\Event;

	use Edde\Api\Application\IApplication;

	class ErrorEvent extends ApplicationEvent {
		/**
		 * @var \Exception
		 */
		protected $exception;

		public function __construct(IApplication $application, \Exception $exception) {
			parent::__construct($application);
			$this->exception = $exception;
		}

		/**
		 * @return \Exception
		 */
		public function getException() {
			return $this->exception;
		}
	}
