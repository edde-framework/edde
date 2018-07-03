<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Lazy request url trait.
	 */
	trait LazyRequestUrlTrait {
		/**
		 * @var IRequestUrl
		 */
		protected $requestUrl;

		/**
		 * @param IRequestUrl $requestUrl
		 */
		public function lazyRequestUrl(IRequestUrl $requestUrl) {
			$this->requestUrl = $requestUrl;
		}
	}
