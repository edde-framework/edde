<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Translator\Dictionary\CsvDictionaryConverter;
	use Edde\Ext\Protocol\ElementConverter;
	use Edde\Ext\Template\TemplateConverter;

	class ConverterManagerConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IConverterManager $instance
		 */
		public function configure($instance) {
			$instance->registerConverter($this->container->create(ExceptionConverter::class));
			$instance->registerConverter($this->container->create(TemplateConverter::class));
			$instance->registerConverter($this->container->create(JsonConverter::class));
			$instance->registerConverter($this->container->create(NodeConverter::class));
			$instance->registerConverter($this->container->create(PhpConverter::class));
			$instance->registerConverter($this->container->create(CsvDictionaryConverter::class));
			$instance->registerConverter($this->container->create(XmlConverter::class));
			$instance->registerConverter($this->container->create(ElementConverter::class));
			$instance->registerConverter($this->container->create(PostConverter::class));
		}
	}
