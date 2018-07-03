<?php
	declare(strict_types = 1);

	namespace Edde\Common\Deffered\Event;

	use Edde\Common\Event\AbstractEvent;

	/**
	 * Base event for deffered package events.
	 */
	class DefferedEvent extends AbstractEvent {
		/**
		 * source class
		 *
		 * @var mixed
		 */
		protected $instance;

		/**
		 * DefferedEvent constructor.
		 *
		 * @param mixed $instance
		 */
		public function __construct($instance) {
			$this->instance = $instance;
		}

		/**
		 * return source of this event
		 *
		 * @return mixed
		 */
		public function getInstance() {
			return $this->instance;
		}
	}
