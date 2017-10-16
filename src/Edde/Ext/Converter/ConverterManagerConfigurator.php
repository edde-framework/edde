<?php
	namespace Edde\Ext\Converter;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Converter\IConverterManager;
		use Edde\Common\Config\AbstractConfigurator;

		class ConverterManagerConfigurator extends AbstractConfigurator {
			use Container;

			/**
			 * @param IConverterManager $instance
			 *
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			public function configure($instance) {
				parent::configure($instance);
				$instance->registerConverter($this->container->create(JsonDecodeConverter::class, [], __METHOD__));
			}
		}
