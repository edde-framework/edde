<?php
	declare(strict_types=1);

	namespace Edde\Ext\Upgrade;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Common\Config\AbstractConfigurator;

	class UpgradeManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IUpgradeManager $instance
		 *
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerUpgrade($this->container->create(InitialStorageUpgrade::class));
		}
	}
