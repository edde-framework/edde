<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application\Event;

	use Edde\Api\Application\IApplication;

	class FinishEvent extends ApplicationEvent {
		/**
		 * @var mixed
		 */
		protected $result;

		public function __construct(IApplication $application, $result) {
			parent::__construct($application);
			$this->result = $result;
		}

		/**
		 * @return mixed
		 */
		public function getResult() {
			return $this->result;
		}
	}
