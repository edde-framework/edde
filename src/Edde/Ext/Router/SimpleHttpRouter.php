<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\LazyContextTrait;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Runtime\LazyRuntimeTrait;
	use Edde\Common\Application\HttpResponseHandler;
	use Edde\Common\Protocol\Request\Message;
	use Edde\Common\Router\AbstractRouter;
	use Edde\Common\Strings\StringUtils;

	class SimpleHttpRouter extends AbstractRouter {
		use LazyHttpRequestTrait;
		use LazyResponseManagerTrait;
		use LazyContainerTrait;
		use LazyRuntimeTrait;
		use LazyContextTrait;

		/**
		 * @inheritdoc
		 */
		public function createRequest() {
			if ($this->runtime->isConsoleMode()) {
				return null;
			}
			$requestUrl = $this->httpRequest->getRequestUrl();
			if (empty($pathList = $requestUrl->getPathList())) {
				return null;
			}
			if (count($pathList) !== 2) {
				return null;
			}
			list($control, $action) = $pathList;
			$partList = [];
			foreach (explode('.', $control) as $part) {
				$partList[] = StringUtils::toCamelCase($part);
			}
			$name = implode('\\', $partList);
			$parameterList = $requestUrl->getParameterList();
			foreach ($this->context->cascade('\\', $name) as $class) {
				if (class_exists($class)) {
					$this->responseManager->setResponseHandler($this->container->create(HttpResponseHandler::class));
					return (new Message($class . '::action' . StringUtils::toCamelCase($action)))->data($parameterList)->setValue($this->httpRequest->getContent());
				}
			}
			return null;
		}
	}
