<?php
	declare(strict_types=1);
	namespace App\Common\Router;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Router\IRouterService;
		use Edde\Common\Config\AbstractConfigurator;
		use Edde\Common\Element\Message;
		use Edde\Ext\Router\ProtocolServiceRouter;
		use Edde\Ext\Router\StaticRouter;

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
				$controller = 'Index\Http\IndexController';
				$method = 'actionIndex';
				$instance->registerRouterList([
					/**
					 * because whole application is built around The Protocol implementation, also router is bound to one of
					 * protocol's services to check, if the system is able to handle current request
					 */
					$this->container->create(ProtocolServiceRouter::class, [], __METHOD__),
					/**
					 * last router is considered as a default
					 */
					$this->container->create(StaticRouter::class, [
						(new Message($controller . '::' . $method))->mergeMetaList([
							'::class'  => $controller,
							'::method' => $method,
						]),
					], __METHOD__),
				]);
			}
		}
