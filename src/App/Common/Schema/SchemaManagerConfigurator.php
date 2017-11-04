<?php
	namespace App\Common\Schema;

		use App\Api\User\Schema\UserRoleSchema;
		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Schema\Exception\UnknownSchemaException;
		use Edde\Api\Schema\ISchemaManager;

		class SchemaManagerConfigurator extends \Edde\Ext\Schema\SchemaManagerConfigurator {
			/**
			 * @param ISchemaManager $instance
			 *
			 * @throws ContainerException
			 * @throws FactoryException
			 * @throws UnknownSchemaException
			 */
			public function configure($instance) {
				parent::configure($instance);
				/**
				 * list of preloaded schemas as they enable the others to see each other
				 */
				$list = [
					UserRoleSchema::class,
				];
				foreach ($list as $name) {
					$instance->load($name);
				}
			}
		}
