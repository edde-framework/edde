<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Converter;

	use Edde\Api\Converter\IConverterManager;
	use Edde\Config\AbstractConfigurator;
	use Edde\Ext\Bus\JsonDecodeConverter as ElementJsonDecodeConverter;
	use Edde\Ext\Bus\JsonEncodeConverter as ElementJsonEncodeConverter;
	use Edde\Ext\Converter\JsonDecodeConverter;
	use Edde\Ext\Converter\JsonEncodeConverter;
	use Edde\Inject\Container\Container;

	class ConverterManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IConverterManager $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerConverter($this->container->create(JsonDecodeConverter::class, [], __METHOD__));
			$instance->registerConverter($this->container->create(JsonEncodeConverter::class, [], __METHOD__));
			$instance->registerConverter($this->container->create(ElementJsonDecodeConverter::class, [], __METHOD__));
			$instance->registerConverter($this->container->create(ElementJsonEncodeConverter::class, [], __METHOD__));
		}
	}
