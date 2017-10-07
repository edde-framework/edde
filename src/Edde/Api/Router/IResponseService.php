<?php
	declare(strict_types=1);
	namespace Edde\Api\Router;

	interface IResponseService {
		/**
		 * physically execute the response (for example send http headers, echo the body, ...)
		 * method should return same response as it's input; the response is not processed later
		 *
		 * @param IResponse $response
		 *
		 * @return IResponse
		 */
		public function execute(IResponse $response): IResponse;
	}
