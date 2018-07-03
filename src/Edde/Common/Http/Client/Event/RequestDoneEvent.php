<?php
	declare(strict_types=1);

	namespace Edde\Common\Http\Client\Event;

	use Edde\Api\Http\Client\IHttpHandler;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;

	class RequestDoneEvent extends HandlerEvent {
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;
		/**
		 * @var float
		 */
		protected $time;

		public function __construct(IHttpRequest $httpRequest, IHttpHandler $httpHandler, IHttpResponse $httpResponse, float $time) {
			parent::__construct($httpRequest, $httpHandler);
			$this->httpResponse = $httpResponse;
			$this->time = $time;
		}

		public function getHttpResponse(): IHttpResponse {
			return $this->httpResponse;
		}

		public function getTime(): float {
			return $this->time;
		}
	}
