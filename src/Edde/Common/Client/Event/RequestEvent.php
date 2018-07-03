<?php
	declare(strict_types = 1);

	namespace Edde\Common\Client\Event;

	use Edde\Api\Http\IHttpRequest;

	/**
	 * Sent when http request was created (can be used for header alternation/....).
	 */
	class RequestEvent extends ClientEvent {
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;

		/**
		 * @param IHttpRequest $httpRequest
		 */
		public function __construct(IHttpRequest $httpRequest) {
			$this->httpRequest = $httpRequest;
		}

		public function getHttpRequest(): IHttpRequest {
			return $this->httpRequest;
		}
	}
