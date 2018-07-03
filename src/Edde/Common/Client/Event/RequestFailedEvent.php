<?php
	declare(strict_types = 1);

	namespace Edde\Common\Client\Event;

	use Edde\Api\Client\IHttpHandler;
	use Edde\Api\Http\IHttpRequest;

	class RequestFailedEvent extends HandlerEvent {
		/**
		 * how long request was running before fail
		 *
		 * @var float
		 */
		protected $time;

		public function __construct(IHttpRequest $httpRequest, IHttpHandler $httpHandler, float $time) {
			parent::__construct($httpRequest, $httpHandler);
			$this->time = $time;
		}

		public function getTime(): float {
			return $this->time;
		}
	}
