<?php
	declare(strict_types=1);
	namespace Edde\Api\Request;

		use Edde\Api\Config\IConfigurable;
		use Edde\Api\Response\IResponse;

		/**
		 * Request service is responsible for request to response translation; it should
		 * create response to current request, which should be later processed by Response service.
		 */
		interface IRequestService extends IConfigurable {
			/**
			 * @param IRequest $request
			 *
			 * @return \Edde\Api\Response\IResponse
			 */
			public function execute(IRequest $request): IResponse;
		}
