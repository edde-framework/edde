<?php
	namespace Edde\Api\Http;

		use Edde\Api\Config\IConfigurable;

		interface IResponseService extends IConfigurable {
			/**
			 * execute a http response
			 *
			 * @param IResponse $response
			 *
			 * @return IResponseService
			 */
			public function execute(IResponse $response): IResponseService;
		}
