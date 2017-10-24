<?php
	namespace Edde\Ext\Schema;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Schema\ISchemaManager;
		use Edde\Common\Config\AbstractConfigurator;

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
