<?php
	declare(strict_types=1);
	namespace Edde\Ext\Router;

	use Edde\Element\IRequest;
	use Edde\Inject\Container\Container;
	use Edde\Router\AbstractRouter;

	class ProxyRouter extends AbstractRouter {
		use Container;
		/** @var string */
		protected $proxy;
		/** @var array */
		protected $parameters = [];
		/** @var \Edde\Router\IRouter */
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
