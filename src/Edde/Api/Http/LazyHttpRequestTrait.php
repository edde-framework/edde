<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	/**
	 * Lazy http request dependency.
	 */
	trait LazyHttpRequestTrait {
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;

		/**
		 * @param IHttpRequest $httpRequest
		 */
		public function lazyHttpRequest(IHttpRequest $httpRequest) {
			$this->httpRequest = $httpRequest;
		}
	}
