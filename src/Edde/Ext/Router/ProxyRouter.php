<?php
	declare(strict_types=1);
	namespace Edde\Ext\Router;

		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Request\IRequest;
		use Edde\Api\Router\IRouter;
		use Edde\Common\Router\AbstractRouter;

		class ProxyRouter extends AbstractRouter {
			use Container;
			/**
			 * @var string
			 */
			protected $proxy;
			protected $parameterList = [];
			/**
			 * @var IRouter
			 */
			protected $router;

			public function __construct(string $proxy, array $parameterList = []) {
				$this->proxy = $proxy;
				$this->parameterList = $parameterList;
			}

			/**
			 * @inheritdoc
			 */
			public function canHandle() : bool {
				return $this->router->canHandle();
			}

			/**
			 * @inheritdoc
			 */
			public function createRequest() : IRequest {
				return $this->router->createRequest();
			}

			/**
			 * @inheritdoc
			 */
			protected function handleSetup() : void {
				parent::handleSetup();
				$this->router = $this->container->create($this->proxy, $this->parameterList, __METHOD__);
				$this->router->setup();
			}
		}
