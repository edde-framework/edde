<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Crate\LazyCrateFactoryTrait;
	use Edde\Api\Http\LazyBodyTrait;
	use Edde\Api\Http\LazyHeaderListTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Http\LazyPostListTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Api\Runtime\LazyRuntimeTrait;
	use Edde\Common\Application\Request;
	use Edde\Common\Router\AbstractRouter;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Simple http router implementation without any additional magic.
	 */
	class HttpRouter extends AbstractRouter {
		use LazyResponseManagerTrait;
		use LazyBodyTrait;
		use LazyRequestUrlTrait;
		use LazyHeaderListTrait;
		use LazyPostListTrait;
		use LazyHttpRequestTrait;
		use LazyHttpResponseTrait;
		use LazyRuntimeTrait;
		use LazyCrateFactoryTrait;

		/**
		 * @inheritdoc
		 */
		public function createRequest() {
			$this->use();
			if ($this->runtime->isConsoleMode()) {
				return null;
			}
			$this->httpResponse->setContentType($mime = $this->headerList->getContentType()
				->getMime($this->headerList->getAccept()));
			$this->responseManager->setMime($mime = ('http+' . $mime));
			$parameterList = $this->requestUrl->getQuery();
			if (isset($parameterList['action']) === false && isset($parameterList['handle']) === false) {
				return null;
			}
			if ($this->httpRequest->isMethod('GET') === false && ($source = ($this->postList->isEmpty() ? $this->body->convert('array') : $this->postList->array())) !== null) {
				/**
				 * support for control property filling
				 */
				if (isset($source[''])) {
					$parameterList[null] = $source[''];
					unset($source['']);
				}
				if (empty($source) === false) {
					$parameterList = array_merge($parameterList, $this->crateFactory->build($source));
				}
			}
			$request = new Request($mime);
			if (isset($parameterList['handle'])) {
				list($control, $handle) = explode('.', $parameterList['handle']);
				unset($parameterList['handle']);
				$request->registerHandleHandler($control, 'handle' . StringUtils::toCamelCase($handle), $parameterList);
			}
			if (isset($parameterList['action'])) {
				list($control, $action) = explode('.', $parameterList['action']);
				unset($parameterList['action']);
				$request->registerActionHandler($control, 'action' . StringUtils::toCamelCase($action), $parameterList);
			}
			return $request;
		}
	}
