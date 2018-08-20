<?php
	declare(strict_types=1);
	namespace Edde\Service\Application;

	use Edde\Application\IRequestService;

	trait RequestService {
		/** @var IRequestService */
		protected $requestService;

		/**
		 * @param IRequestService $requestService
		 */
		public function injectRequestService(IRequestService $requestService): void {
			$this->requestService = $requestService;
		}
	}
