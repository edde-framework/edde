<?php
	declare(strict_types=1);
	namespace Edde\Converter;

	use Edde\Config\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Service\Container\Container;

	class ConverterManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IConverterManager $instance
		 *
		 * @throws ContainerException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerConverter($this->container->create(JsonDecodeConverter::class, [], __METHOD__));
			$instance->registerConverter($this->container->create(JsonEncodeConverter::class, [], __METHOD__));
		}
	}
