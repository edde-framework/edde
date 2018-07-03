<?php
	declare(strict_types=1);

	namespace Edde\Api\Http\Inject;

	use Edde\Api\Http\IHttpService;

	trait HttpService {
		/**
		 * @var IHttpService
		 */
		protected $httpService;

		/**
		 * @param IHttpService $httpService
		 */
		public function lazyHttpService(IHttpService $httpService) {
			$this->httpService = $httpService;
		}
	}
