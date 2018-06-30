<?php
	declare(strict_types=1);
	namespace Edde\Router;

	use Edde\Application\IRequest;
	use Edde\Edde;

	class RouterService extends Edde implements IRouterService {
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
			return ($this->router = $this->getRouter()) !== null;
		}

		/** @inheritdoc */
		public function createRequest(): IRequest {
			if ($this->request) {
				return $this->request;
			}
			if ($this->router === null && ($this->router = $this->getRouter()) === null) {
				throw new RouterException('Cannot handle current request.');
			}
			return $this->request = $this->router->createRequest();
		}
	}
