<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Event\IEventBus;
	use Edde\Api\Protocol\IProtocolService;
	use Edde\Api\Request\IRequestService;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Protocol\PacketProtocolHandler;

	class ProtocolServiceConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IProtocolService $instance
		 *
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function configure($instance) {
			$instance->registerProtocolHandler($this->container->create(IEventBus::class));
			$instance->registerProtocolHandler($this->container->create(IRequestService::class));
			$instance->registerProtocolHandler($this->container->create(PacketProtocolHandler::class));
		}
	}
