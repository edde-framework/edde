<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Ext\Rest\ProtocolService;
	use Edde\Ext\Rest\ThreadService;

	class RestRouterConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param RestRouter $instance
		 */
		public function configure($instance) {
			$instance->registerService($this->container->create(ProtocolService::class));
			$instance->registerService($this->container->create(ThreadService::class));
		}
	}
