<?php
	declare(strict_types=1);
	namespace Edde\Api\Request;

		use Edde\Api\Config\IConfigurable;

		/**
		 * Request service is responsible for request to response translation; it should
		 * create response to current request, which should be later processed by Response service.
		 */
		interface IRequestService extends IConfigurable {
			/**
			 * shortened method to execute current request (basically run(getRequest()))
			 *
			 * @return IRequestService
			 */
			public function execute(): IRequestService;

			/**
			 * @param IRequest $request
			 *
			 * @return IRequestService
			 */
			public function run(IRequest $request): IRequestService;

			/**
			 * get current request (current is during and after execute)
			 *
			 * @return IRequest
			 */
			public function getRequest(): IRequest;
		}
