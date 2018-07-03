<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Lazy host url dependency.
	 */
	trait LazyHostUrlTrait {
		/**
		 * @var IHostUrl
		 */
		protected $hostUrl;

		/**
		 * @param IHostUrl $hostUrl
		 */
		public function lazyHostUrl(IHostUrl $hostUrl) {
			$this->hostUrl = $hostUrl;
		}
	}
