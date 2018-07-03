<?php
	declare(strict_types=1);

	namespace Edde\Ext\Resource;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Ext\Control\ControlTemplateResourceProvider;
	use Edde\Ext\Template\TemplateResourceProvider;
	use Edde\Ext\Web\ImageResourceProvider;
	use Edde\Ext\Web\JavaScriptResourceProvider;
	use Edde\Ext\Web\StyleSheetResourceProvider;

	class ResourceManagerConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IResourceManager $instance
		 */
		public function configure($instance) {
			$instance->registerResourceProvider($this->container->create(ControlTemplateResourceProvider::class, [], __METHOD__));
			$instance->registerResourceProvider($this->container->create(TemplateResourceProvider::class, [], __METHOD__));
			$instance->registerResourceProvider($this->container->create(StyleSheetResourceProvider::class, [], __METHOD__));
			$instance->registerResourceProvider($this->container->create(JavaScriptResourceProvider::class, [], __METHOD__));
			$instance->registerResourceProvider($this->container->create(ImageResourceProvider::class, [], __METHOD__));
		}
	}
