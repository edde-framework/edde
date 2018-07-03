<?php
	declare(strict_types=1);

	namespace Edde\Ext\Link;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Ext\Router\RestRouter;

	class LinkFactoryConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param ILinkFactory $instance
		 */
		public function configure($instance) {
			$instance->registerLinkGenerator($this->container->create(RestRouter::class));
		}
	}
