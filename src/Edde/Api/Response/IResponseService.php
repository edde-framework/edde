<?php
	declare(strict_types=1);
	namespace Edde\Api\Response;

		interface IResponseService {
			/**
			 * physically execute the response (for example send http headers, echo the body, ...)
			 * method should return same response as it's input; the response is not processed later
			 *
			 * @param IResponse $response
			 *
			 * @return IResponseService
			 */
			public function execute(IResponse $response): IResponseService;
		}
