<?php
	declare(strict_types=1);
	namespace Edde\Ext\Bus;

	use Edde\Api\Bus\Event\IEventBus;
	use Edde\Api\Bus\IMessageBus;
	use Edde\Api\Bus\IMessageService;
	use Edde\Api\Bus\Request\IRequestService;
	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Common\Config\AbstractConfigurator;

	class MessageBusConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @var $instance IMessageBus
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
