<?php
	declare(strict_types=1);

	namespace Edde\Api\Http\Client\Inject;

	use Edde\Api\Http\Client\IHttpClient;

	/**
	 * Lazy http client dependency.
	 */
	trait HttpClient {
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
