<?php
	declare(strict_types=1);
	namespace Edde\Service\Bus;

	use Edde\Bus\IRequestService;

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
