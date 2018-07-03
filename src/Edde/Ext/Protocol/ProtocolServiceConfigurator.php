<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\Event\IEventBus;
	use Edde\Api\Protocol\IProtocolService;
	use Edde\Api\Protocol\Request\IRequestService;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Protocol\PacketProtocolHandler;

	class ProtocolServiceConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IProtocolService $instance
		 */
		public function configure($instance) {
			$instance->registerProtocolHandler($this->container->create(IEventBus::class));
			$instance->registerProtocolHandler($this->container->create(IRequestService::class));
			$instance->registerProtocolHandler($this->container->create(PacketProtocolHandler::class));
		}
	}
