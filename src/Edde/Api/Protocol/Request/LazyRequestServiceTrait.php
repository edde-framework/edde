<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Request;

	trait LazyRequestServiceTrait {
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
