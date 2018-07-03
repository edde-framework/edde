<?php
	declare(strict_types=1);

	namespace Edde\Api\Http\Inject;

	use Edde\Api\Http\IHostUrl;

	trait HostUrl {
		/**
		 * @var IHostUrl
		 */
		protected $hostUrl;

		public function lazyHostUrl(IHostUrl $hostUrl) {
			$this->hostUrl = $hostUrl;
		}
	}
