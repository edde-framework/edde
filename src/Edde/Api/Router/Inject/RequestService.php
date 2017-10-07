<?php
	declare(strict_types=1);
	namespace Edde\Api\Router\Inject;

	use Edde\Api\Router\IRequestService;

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
