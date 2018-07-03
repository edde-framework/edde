<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	/**
	 * Lazy http request dependency.
	 */
	trait LazyHttpResponseTrait {
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;

		/**
		 * @param IHttpResponse $httpResponse
		 */
		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}
	}
