<?php
	namespace Edde\Api\Http\Inject;

		use Edde\Api\Http\IResponseService;

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
