<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Runtime\LazyRuntimeTrait;
	use Edde\Common\Application\HttpResponseHandler;
	use Edde\Common\Protocol\Request\Message;
	use Edde\Common\Router\AbstractRouter;

	/**
	 * Simple http router implementation without any additional magic.
	 */
	class HttpRouter extends AbstractRouter {
		use LazyHttpRequestTrait;
		use LazyContainerTrait;
		use LazyResponseManagerTrait;
		use LazyRuntimeTrait;

		/**
		 * @inheritdoc
		 */
		public function createRequest() {
			if ($this->runtime->isConsoleMode()) {
				return null;
			}
			$requestUrl = $this->httpRequest->getRequestUrl();
			$parameterList = $requestUrl->getParameterList();
			if (isset($parameterList['action']) === false) {
				return null;
			}
			list($control, $action) = explode('.', $parameterList['action']);
			$this->responseManager->setResponseHandler($this->container->create(HttpResponseHandler::class));
			unset($parameterList['action']);
			return (new Message($control . '/' . $action))->data($parameterList)->setValue($this->httpRequest->getContent());
		}
	}
