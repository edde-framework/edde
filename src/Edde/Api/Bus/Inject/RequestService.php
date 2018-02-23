<?php
	declare(strict_types=1);
	namespace Edde\Api\Bus\Inject;

	use Edde\Api\Bus\Request\IRequestService;

	trait RequestService {
		/** @var IRequestService */
		protected $requestService;

		/**
		 * @param IRequestService $requestService
		 */
		public function lazyRequestService(IRequestService $requestService): void {
			$this->requestService = $requestService;
		}
	}
