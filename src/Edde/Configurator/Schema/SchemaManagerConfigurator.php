<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Schema;

	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Container\FactoryException;
	use Edde\Ext\Schema\SchemaReflectionLoader;
	use Edde\Inject\Container\Container;

	class SchemaManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param ISchemaManager $instance
		 *
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerSchemaLoader($this->container->create(SchemaReflectionLoader::class, [], __METHOD__));
		}
	}
