<?php
	declare(strict_types=1);

	namespace Edde\Api\Router;

	trait LazyRouterServiceTrait {
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
