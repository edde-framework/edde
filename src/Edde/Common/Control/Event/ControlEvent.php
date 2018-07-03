<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control\Event;

	use Edde\Api\Control\IControl;
	use Edde\Common\Event\AbstractEvent;

	class ControlEvent extends AbstractEvent {
		/**
		 * @var IControl
		 */
		protected $control;

		/**
		 * @param IControl $control
		 */
		public function __construct(IControl $control) {
			$this->control = $control;
		}

		public function getControl(): IControl {
			return $this->control;
		}
	}
