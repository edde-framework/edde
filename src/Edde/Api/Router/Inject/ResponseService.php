<?php
	declare(strict_types=1);
	namespace Edde\Api\Router\Inject;

	use Edde\Api\Router\IResponseService;

	trait ResponseService {
		/**
		 * @var IResponseService
		 */
		protected $responseService;

		/**
		 * @param IResponseService $responseService
		 */
		public function lazyResponseService(IResponseService $responseService) {
			$this->responseService = $responseService;
		}
	}
