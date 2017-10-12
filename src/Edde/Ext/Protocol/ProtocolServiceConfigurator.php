<?php
	namespace Edde\Ext\Protocol;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Protocol\IProtocolService;
		use Edde\Common\Config\AbstractConfigurator;

		class ProtocolServiceConfigurator extends AbstractConfigurator {
			use Container;

			/**
			 * @param IProtocolService $instance
			 *
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			public function configure($instance) {
				parent::configure($instance);
				$instance->registerProtocolHandlerList([
					$this->container->create(StaticHandler::class, [], __METHOD__),
				]);
			}
		}
