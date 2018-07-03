<?php
	declare(strict_types=1);

	namespace Edde\Api\Router;

	use Edde\Api\Application\IResponseHandler;
	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Protocol\IElement;

	/**
	 * Implementation of application router service.
	 */
	interface IRouterService extends IConfigurable {
		/**
		 * routers should be created on demand
		 *
		 * @param string $router
		 * @param array  $parameterList
		 *
		 * @return IRouterService
		 */
		public function registerRouter(string $router, array $parameterList = []): IRouterService;

		/**
		 * when routers fail, execute this default request
		 *
		 * @param IElement         $element
		 * @param IResponseHandler $responseHandler
		 *
		 * @return IRouterService
		 */
		public function setDefaultRequest(IElement $element, IResponseHandler $responseHandler = null): IRouterService;

		/**
		 * @return IElement
		 */
		public function createRequest(): IElement;

		/**
		 * get current class being executed
		 *
		 * @return string
		 */
		public function getCurrentClass(): string;

		/**
		 * return current method name being executed
		 *
		 * @return string
		 */
		public function getCurrentMethod(): string;
	}
