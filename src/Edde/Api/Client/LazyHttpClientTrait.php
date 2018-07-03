<?php
	declare(strict_types = 1);
	namespace Edde\Api\Client;

	/**
	 * Lazy http client dependency.
	 */
	trait LazyHttpClientTrait {
		/**
		 * @var IHttpClient
		 */
		protected $httpClient;

		/**
		 * @param IHttpClient $httpClient
		 */
		public function lazyHttpClient(IHttpClient $httpClient) {
			$this->httpClient = $httpClient;
		}
	}
