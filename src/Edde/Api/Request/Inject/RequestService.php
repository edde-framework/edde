<?php
	declare(strict_types=1);

	namespace Edde\Api\Request\Inject;

	use Edde\Api\Request\IRequestService;

	trait RequestService {
		/**
		 * @var IRequestService
		 */
		protected $requestService;

		/**
		 * @param IRequestService $requestService
		 */
		public function lazyRequestService(IRequestService $requestService) {
			$this->requestService = $requestService;
		}
	}
