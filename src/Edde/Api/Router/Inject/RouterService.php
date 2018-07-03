<?php
	declare(strict_types=1);

	namespace Edde\Api\Router\Inject;

	use Edde\Api\Router\IRouterService;

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
