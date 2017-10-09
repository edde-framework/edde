<?php
	declare(strict_types=1);
	namespace App\Common\Router;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Router\IRouterService;
		use Edde\Common\Config\AbstractConfigurator;
		use Edde\Common\Element\Message;
		use Edde\Common\Router\StaticRouter;

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
				 * last router is considered as a default
				 */
				$instance->registerRouter($this->container->create(StaticRouter::class, [new Message('index.index-view/action-index')], __METHOD__));
			}
		}
