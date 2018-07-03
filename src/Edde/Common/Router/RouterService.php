<?php
	declare(strict_types = 1);

	namespace Edde\Common\Router;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Router\RouterException;

	/**
	 * Default implementation of a router service.
	 */
	class RouterService extends RouterList implements IRouterService {
		/**
		 * @var IRequest
		 */
		protected $request;

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws RouterException
		 */
		public function createRequest() {
			$this->use();
			if ($this->request) {
				return $this->request;
			}
			$e = null;
			foreach ($this->routerList as $router) {
				if (($request = $router->createRequest()) !== null) {
					return $this->request = $request;
				}
			}
			throw new BadRequestException('Cannot handle current application request.', 0, $e);
		}
	}
