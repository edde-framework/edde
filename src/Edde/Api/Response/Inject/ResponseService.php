<?php
	declare(strict_types=1);
	namespace Edde\Api\Response\Inject;

		use Edde\Api\Response\IResponseService;

		trait ResponseService {
			/**
			 * @var IResponseService
			 */
			protected $responseService;

			/**
			 * @param IResponseService $responseService
			 */
			public function lazyResponseService(IResponseService $responseService) : void {
				$this->responseService = $responseService;
			}
		}
