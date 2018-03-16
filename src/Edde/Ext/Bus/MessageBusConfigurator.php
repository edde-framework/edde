<?php
	declare(strict_types=1);
	namespace Edde\Ext\Bus;

	use Edde\Api\Bus\Event\IEventBus;
	use Edde\Api\Bus\IMessageBus;
	use Edde\Api\Bus\IMessageService;
	use Edde\Api\Bus\Request\IRequestService;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Container\FactoryException;
	use Edde\Inject\Container\Container;

	class MessageBusConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param $instance IMessageBus
		 *
		 * @throws ContainerException
		 * @throws FactoryException
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
