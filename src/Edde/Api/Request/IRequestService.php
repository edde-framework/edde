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
			 * shortened method to execute current request (basically run(getRequest()))
			 *
			 * @return IResponse
			 */
			public function execute(): IResponse;

			/**
			 * @param IRequest $request
			 *
			 * @return IResponse
			 */
			public function run(IRequest $request): IResponse;

			/**
			 * get current request (current is during and after execute)
			 *
			 * @return IRequest
			 */
			public function getRequest(): IRequest;
		}
