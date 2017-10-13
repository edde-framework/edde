<?php
	namespace Edde\Api\Http;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Http\Exception\NoHttpException;

		interface IRequestService extends IConfigurable {
			/**
			 * return a singleton instance representing current http request
			 *
			 * @return IRequest
			 *
			 * @throws NoHttpException
			 */
			public function getRequest(): IRequest;
		}
