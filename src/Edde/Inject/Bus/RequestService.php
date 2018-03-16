<?php
	declare(strict_types=1);
	namespace Edde\Inject\Bus;

	use Edde\Bus\IRequestService;

	trait RequestService {
		/** @var IRequestService */
		protected $requestService;

		/**
		 * @param \Edde\Bus\Request\IRequestService $requestService
		 */
		public function lazyRequestService(IRequestService $requestService): void {
			$this->requestService = $requestService;
		}
	}
