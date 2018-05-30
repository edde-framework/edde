<?php
	declare(strict_types=1);
	namespace Edde\Router;

	use Edde\Config\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Service\Container\Container;

	class RouterServiceConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IRouterService $instance
		 *
		 * @throws ContainerException
		 */
		public function configure($instance) {
			parent::configure($instance);
			/**
			 * RequestRouter is able to make route for CLI and for HTTP requests
			 */
			$instance->registerRouter($this->container->create(RequestRouter::class, [], __METHOD__));
		}
	}