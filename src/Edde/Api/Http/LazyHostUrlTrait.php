<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	trait LazyHostUrlTrait {
		/**
		 * @var IHostUrl
		 */
		protected $hostUrl;

		public function lazyHostUrl(IHostUrl $hostUrl) {
			$this->hostUrl = $hostUrl;
		}
	}
