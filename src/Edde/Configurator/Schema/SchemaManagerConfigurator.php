<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Schema;

	use Edde\Config\AbstractConfigurator;
	use Edde\Inject\Container\Container;
	use Edde\Schema\ISchemaManager;
	use Edde\Schema\SchemaReflectionLoader;

	class SchemaManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param ISchemaManager $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerSchemaLoader($this->container->create(SchemaReflectionLoader::class, [], __METHOD__));
		}
	}
