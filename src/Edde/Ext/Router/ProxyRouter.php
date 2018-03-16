<?php
	declare(strict_types=1);
	namespace Edde\Ext\Router;

	use Edde\Api\Bus\Request\IRequest;
	use Edde\Api\Router\IRouter;
	use Edde\Common\Router\AbstractRouter;
	use Edde\Inject\Container\Container;

	class ProxyRouter extends AbstractRouter {
		use Container;
		/** @var string */
		protected $proxy;
		/** @var array */
		protected $parameters = [];
		/** @var IRouter */
		protected $router;

		public function __construct(string $proxy, array $parameters = []) {
			$this->proxy = $proxy;
			$this->parameters = $parameters;
		}

		/** @inheritdoc */
		public function canHandle(): bool {
			return $this->router->canHandle();
		}

		/** @inheritdoc */
		public function createRequest(): IRequest {
			return $this->request ?: $this->request = $this->router->createRequest();
		}

		/** @inheritdoc */
		protected function handleSetup(): void {
			parent::handleSetup();
			$this->router = $this->container->create($this->proxy, $this->parameters, __METHOD__);
			$this->router->setup();
		}
	}
