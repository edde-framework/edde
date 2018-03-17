<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Converter;

	use Edde\Config\AbstractConfigurator;
	use Edde\Converter\IConverterManager;
	use Edde\Converter\JsonEncodeConverter;
	use Edde\Element\Converter\JsonDecodeConverter as ElementJsonDecodeConverter;
	use Edde\Element\Converter\JsonEncodeConverter as ElementJsonEncodeConverter;
	use Edde\Inject\Container\Container;

	class ConverterManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IConverterManager $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerConverter($this->container->create(\Edde\Converter\JsonDecodeConverter::class, [], __METHOD__));
			$instance->registerConverter($this->container->create(JsonEncodeConverter::class, [], __METHOD__));
			$instance->registerConverter($this->container->create(ElementJsonDecodeConverter::class, [], __METHOD__));
			$instance->registerConverter($this->container->create(ElementJsonEncodeConverter::class, [], __METHOD__));
		}
	}
