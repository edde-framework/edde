<?php
	declare(strict_types=1);
	namespace Edde\Inject\Router;

	use Edde\Router\IRouterService;

	trait RouterService {
		/**
		 * @var \Edde\Router\IRouterService
		 */
		protected $routerService;

		/**
		 * @param \Edde\Router\IRouterService $routerService
		 */
		public function lazyRouterService(\Edde\Router\IRouterService $routerService) {
			$this->routerService = $routerService;
		}
	}
