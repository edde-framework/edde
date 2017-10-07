<?php
	declare(strict_types=1);
	namespace Edde\Api\Router;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Request service is responsible for request to response translation; it should
	 * create response to current request, which should be later processed by Response service.
	 */
	interface IRequestService extends IConfigurable {
		/**
		 * @param IRequest $request
		 *
		 * @return IResponse
		 */
		public function execute(IRequest $request): IResponse;
	}
