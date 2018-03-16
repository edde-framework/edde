<?php
	declare(strict_types=1);
	namespace Edde\Inject\Http;

	use Edde\Http\IRequestService;

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
