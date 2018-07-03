<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Router\IRouter;
	use Edde\Api\Router\IRouterList;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	/**
	 * Default implementation of a router list.
	 */
	class RouterList extends Object implements IRouterList {
		use ConfigurableTrait;
		/**
		 * @var IRouter[]
		 */
		protected $routerList = [];

		/**
		 * @inheritdoc
		 */
		public function registerRouter(IRouter $router): IRouterList {
			$this->routerList[] = $router;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function createRequest() {
			foreach ($this->routerList as $router) {
				if (($request = $router->createRequest()) !== null) {
					return $request;
				}
			}
			return null;
		}
	}
