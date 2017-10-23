<?php
	namespace Edde\Ext\Storage;

		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Storage\IEntityManager;
		use Edde\Common\Config\AbstractConfigurator;

		class EntityManagerConfigurator extends AbstractConfigurator {
			use Container;

			/**
			 * @param IEntityManager $instance
			 */
			public function configure($instance) {
				parent::configure($instance);
				$instance->registerGeneratorList([
				]);
			}
		}
