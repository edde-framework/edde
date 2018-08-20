<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Configurable\IConfigurable;

	/**
	 * Simplified request handling service: as it's not necessary to support
	 * plenty of routers by default, this service is responsible for request creation,
	 * thus also opening possibility to use classic routers (like others do).
	 */
	interface IRequestService extends IConfigurable {
		/**
		 * create an application request (should be singleton instance)
		 *
		 * @return IRequest
		 *
		 * @throws ApplicationException if it's not possible to handle current request
		 */
		public function createRequest(): IRequest;
	}
