<?php
	declare(strict_types=1);
	namespace Edde\Service\Router;

	use Edde\Router\IRouterService;

	trait RouterService {
		/** @var IRouterService */
		protected $routerService;

		/**
		 * @param IRouterService $routerService
		 */
		public function injectRouterService(IRouterService $routerService) {
			$this->routerService = $routerService;
		}
	}
