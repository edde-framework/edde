<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Router;

	use Edde\Config\AbstractConfigurator;
	use Edde\Ext\Router\RequestRouter;
	use Edde\Inject\Container\Container;
	use Edde\Router\IRouterService;

	class RouterServiceConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param \Edde\Router\IRouterService $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			/**
			 * RequestRouter is able to make route for CLI and for HTTP requests
			 */
			$instance->registerRouter($this->container->create(RequestRouter::class, [], __METHOD__));
		}
	}
