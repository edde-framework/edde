<?php
	declare(strict_types = 1);

	namespace Edde\Common\Client\Event;

	use Edde\Api\Client\IHttpHandler;
	use Edde\Api\Http\IHttpRequest;

	/**
	 * Event emitted just before client call execution.
	 */
	class OnRequestEvent extends HandlerEvent {
		/**
		 * @var bool
		 */
		protected $cancel;

		public function __construct(IHttpRequest $httpRequest, IHttpHandler $httpHandler) {
			parent::__construct($httpRequest, $httpHandler);
			$this->cancel = false;
		}

		public function cancel(bool $cancel = true) {
			$this->cancel = $cancel;
			return $this;
		}

		public function isCanceled() {
			return $this->cancel;
		}
	}
