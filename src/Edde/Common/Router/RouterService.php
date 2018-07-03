<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Log\Inject\LogService;
	use Edde\Api\Router\Exception\BadRequestException;
	use Edde\Api\Router\IRequest;
	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\IRouterService;

	class RouterService extends AbstractRouter implements IRouterService {
		use LogService;
		/**
		 * @var IRouter[]
		 */
		protected $routerList = [];
		/**
		 * @var IRouter
		 */
		protected $router;
		/**
		 * @var IRequest
		 */
		protected $request;

		/**
		 * @inheritdoc
		 */
		public function registerRouter(IRouter $router): IRouterService {
			$this->routerList[] = $router;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerRouterList(array $routerList): IRouterService {
			foreach ($routerList as $router) {
				$this->registerRouter($router);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getRouter(): IRouter {
			foreach ($this->routerList as $router) {
				if ($router->setup() && $router->canHandle()) {
					return $router;
				}
			}
			return null;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(): bool {
			try {
				return ($this->router = $this->getRouter()) !== null;
			} catch (\Exception $exception) {
				$this->logService->exception($exception, [
					'edde',
					'router-service',
				]);
			}
			return false;
		}

		/**
		 * @inheritdoc
		 */
		public function createRequest(): IRequest {
			if ($this->request) {
				return $this->request;
			}
			if ($this->router === null && ($this->router = $this->getRouter()) === null) {
				throw new BadRequestException('Cannot handle current request.');
			}
			return $this->request = $this->router->createRequest();
		}
	}
