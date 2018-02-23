<?php
	declare(strict_types=1);
	namespace Edde\Ext\Converter;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Ext\Bus\JsonDecodeConverter as ElementJsonDecodeConverter;
	use Edde\Ext\Bus\JsonEncodeConverter as ElementJsonEncodeConverter;

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
			$instance->registerConverter($this->container->create(JsonEncodeConverter::class, [], __METHOD__));
			$instance->registerConverter($this->container->create(ElementJsonDecodeConverter::class, [], __METHOD__));
			$instance->registerConverter($this->container->create(ElementJsonEncodeConverter::class, [], __METHOD__));
		}
	}
