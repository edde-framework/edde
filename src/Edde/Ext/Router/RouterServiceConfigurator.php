<?php
	declare(strict_types=1);
	namespace Edde\Ext\Router;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Config\AbstractConfigurator;

	class RouterServiceConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IRouterService $instance
		 *
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function configure($instance) {
			parent::configure($instance);
			/**
			 * RequestRouter is able to make route for CLI and for HTTP requests
			 */
			$instance->registerRouter($this->container->create(RequestRouter::class, [], __METHOD__));
		}
	}
