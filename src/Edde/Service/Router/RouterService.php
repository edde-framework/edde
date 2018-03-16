<?php
	declare(strict_types=1);
	namespace Edde\Service\Router;

	use Edde\Api\Bus\Request\IRequest;
	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\IRouterService;
	use Edde\Exception\Router\BadRequestException;
	use Edde\Inject\Log\LogService;
	use Edde\Object;

	class RouterService extends Object implements IRouterService {
		use LogService;
		/** @var IRouter[] */
		protected $routers = [];
		/** @var IRouter */
		protected $router;
		/** @var IRequest */
		protected $request;

		/** @inheritdoc */
		public function registerRouter(IRouter $router): IRouterService {
			$this->routers[] = $router;
			return $this;
		}

		/** @inheritdoc */
		public function registerRouters(array $routers): IRouterService {
			foreach ($routers as $router) {
				$this->registerRouter($router);
			}
			return $this;
		}

		/** @inheritdoc */
		public function getRouter(): ?IRouter {
			foreach ($this->routers as $router) {
				if ($router->setup() && $router->canHandle()) {
					return $router;
				}
			}
			return null;
		}

		/** @inheritdoc */
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

		/** @inheritdoc */
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
