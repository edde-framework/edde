<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Ext\Protocol\ElementConverter;

	class ConverterManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IConverterManager $instance
		 *
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function configure($instance) {
			static $converterList = [
				ExceptionConverter::class,
				JsonConverter::class,
				NodeConverter::class,
				PhpConverter::class,
				XmlConverter::class,
				ElementConverter::class,
				PostConverter::class,
			];
			foreach ($converterList as $converter) {
				$instance->registerConverter($this->container->create($converter, [], __METHOD__));
			}
		}
	}
