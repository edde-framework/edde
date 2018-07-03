<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Module\EddeControl;

	class EddeRouter extends HttpRouter {
		use LazyRequestUrlTrait;

		public function createRequest() {
			$this->use();
			if ($this->runtime->isConsoleMode() || $this->requestUrl->getPath() !== '/edde.setup') {
				return null;
			}
			$parameterList = $this->requestUrl->getQuery();
			$parameterList['action'] = EddeControl::class . '.setup';
			$this->requestUrl->setQuery($parameterList);
			return parent::createRequest();
		}
	}
