<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control\Event;

	use Edde\Api\Control\IControl;

	/**
	 * This event should be emitted at the beginning of control handle method.
	 */
	class HandleEvent extends ControlEvent {
		/**
		 * @var string
		 */
		protected $method;
		/**
		 * @var array
		 */
		protected $parameterList;
		/**
		 * is this handler canceled?
		 *
		 * @var bool
		 */
		protected $cancel;

		/**
		 * @param IControl $control
		 * @param string $method
		 * @param array $parameterList
		 */
		public function __construct(IControl $control, $method, array $parameterList) {
			parent::__construct($control);
			$this->method = $method;
			$this->parameterList = $parameterList;
			$this->cancel = false;
		}

		public function getMethod(): string {
			return $this->method;
		}

		public function getParameterList(): array {
			return $this->parameterList;
		}

		public function cancel(bool $cancel = true) {
			$this->cancel = $cancel;
			return $this;
		}

		public function isCanceled(): bool {
			return $this->cancel;
		}
	}
