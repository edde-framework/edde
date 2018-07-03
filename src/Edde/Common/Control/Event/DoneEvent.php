<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control\Event;

	use Edde\Api\Control\IControl;

	/**
	 * When handler is successfull, emit this event.
	 */
	class DoneEvent extends ControlEvent {
		protected $result;

		/**
		 * @param IControl $control
		 * @param mixed $result
		 */
		public function __construct(IControl $control, $result) {
			parent::__construct($control);
			$this->result = $result;
		}

		public function getResult() {
			return $this->result;
		}
	}
