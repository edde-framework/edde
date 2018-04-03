<?php
	declare(strict_types=1);
	namespace Edde\Service\Http;

	use Edde\Http\IRequestService;

	trait RequestService {
		/** @var IRequestService */
		protected $requestService;

		/**
		 * @param IRequestService $requestService
		 */
		public function injectRequestService(IRequestService $requestService) {
			$this->requestService = $requestService;
		}
	}
