<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Schema;

	use Edde\Config\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Schema\ISchemaManager;
	use Edde\Schema\SchemaReflectionLoader;
	use Edde\Service\Container\Container;

	class SchemaManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param ISchemaManager $instance
		 *
		 * @throws ContainerException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerSchemaLoader($this->container->create(SchemaReflectionLoader::class, [], __METHOD__));
		}
	}
