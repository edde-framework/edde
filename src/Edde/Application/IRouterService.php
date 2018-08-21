<?php
	declare(strict_types=1);
	namespace Edde\Application;

	use Edde\Configurable\IConfigurable;
	use Edde\Runtime\RuntimeException;
	use Edde\Url\UrlException;

	/**
	 * Simplified request handling service: as it's not necessary to support
	 * plenty of routers by default, this service is responsible for request creation,
	 * thus also opening possibility to use classic routers (like others do).
	 */
	interface IRouterService extends IConfigurable {
		/**
		 * create an application request from current environment (http/cli; should be singleton instance)
		 *
		 * @return IRequest
		 *
		 * @throws RouterException if it's not possible to handle current request
		 * @throws RuntimeException
		 * @throws UrlException
		 */
		public function createRequest(): IRequest;
	}
