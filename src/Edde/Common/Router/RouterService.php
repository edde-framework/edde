<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Application\IResponseHandler;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\IRouterService;
	use Edde\Api\Router\RouterException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Default implementation of a router service.
	 */
	class RouterService extends Object implements IRouterService {
		use LazyContainerTrait;
		use LazyResponseManagerTrait;
		use ConfigurableTrait;
		/**
		 * @var string[]
		 */
		protected $routerList = [];
		/**
		 * @var IElement
		 */
		protected $defaultRequest;
		/**
		 * @var IResponseHandler
		 */
		protected $defaultResponseHandler;
		/**
		 * @var IElement
		 */
		protected $request;

		/**
		 * @inheritdoc
		 */
		public function registerRouter(string $router, array $parameterList = []): IRouterService {
			$this->routerList[$router] = [
				$router,
				$parameterList,
			];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setDefaultRequest(IElement $element, IResponseHandler $responseHandler = null): IRouterService {
			$this->defaultRequest = $element;
			$this->defaultResponseHandler = $responseHandler;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws RouterException
		 */
		public function createRequest(): IElement {
			if ($this->request) {
				return $this->request;
			}
			foreach ($this->routerList as $router) {
				list($class, $parameterList) = $router;
				/** @var $router IRouter */
				$router = $this->container->create($class, $parameterList, __METHOD__);
				$router->setup();
				if (($this->request = $router->createRequest()) !== null) {
					return $this->request;
				}
			}
			if ($this->defaultRequest) {
				$this->defaultResponseHandler ? $this->responseManager->setResponseHandler($this->defaultResponseHandler) : null;
				return $this->request = $this->defaultRequest;
			}
			throw new BadRequestException('Cannot handle current application request.' . (empty($this->routerList) ? ' There are no registered routers.' : ''));
		}

		/**
		 * @inheritdoc
		 */
		public function getCurrentClass(): string {
			return $this->request->getMeta('::class');
		}

		/**
		 * @inheritdoc
		 */
		public function getCurrentMethod(): string {
			return StringUtils::recamel($this->request->getMeta('::method'));
		}
	}
