<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Request\IRequestService;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Ext\Protocol\Request\ClassRequestHandler;
	use Edde\Ext\Protocol\Request\SimpleRequestHandler;

	class RequestServiceConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IRequestService $instance
		 *
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerRequestHandler($this->container->create(SimpleRequestHandler::class, [], __METHOD__));
			$instance->registerRequestHandler($this->container->create(ClassRequestHandler::class, [], __METHOD__));
		}
	}
