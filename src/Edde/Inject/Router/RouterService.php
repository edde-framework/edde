<?php
	declare(strict_types=1);
	namespace Edde\Inject\Router;

	use Edde\Router\IRouterService;

	trait RouterService {
		/**
		 * @var IRouterService
		 */
		protected $routerService;

		/**
		 * @param IRouterService $routerService
		 */
		public function lazyRouterService(IRouterService $routerService) {
			$this->routerService = $routerService;
		}
	}
