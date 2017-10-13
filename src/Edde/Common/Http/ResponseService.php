<?php
	namespace Edde\Common\Http;

		use Edde\Api\Http\IResponse;
		use Edde\Api\Http\IResponseService;
		use Edde\Api\Response\Inject\ResponseService as ResponseResponseService;
		use Edde\Common\Object\Object;
		use Edde\Common\Response\Response;

		class ResponseService extends Object implements IResponseService {
			use ResponseResponseService;

			/**
			 * @inheritdoc
			 */
			public function execute(IResponse $response): IResponseService {
				$this->responseService->execute(new Response($response->getContent()));
				return $this;
			}
		}
