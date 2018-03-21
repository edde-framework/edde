<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Bus;

	use Edde\Bus\IEventBus;
	use Edde\Bus\IMessageService;
	use Edde\Bus\IRequestService;
	use Edde\Config\AbstractConfigurator;
	use Edde\Service\Container\Container;

	class MessageBusConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param $instance \Edde\Bus\IMessageBus
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerHandlers([
				$this->container->create(IEventBus::class, [], __METHOD__),
				$this->container->create(IRequestService::class, [], __METHOD__),
				$this->container->create(IMessageService::class, [], __METHOD__),
			]);
		}
	}
