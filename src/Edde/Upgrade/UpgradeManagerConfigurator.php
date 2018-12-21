<?php
	declare(strict_types=1);
	namespace Edde\Upgrade;

	use Edde\Configurable\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Service\Container\Container;
	use Edde\Upgrades\MessageQueueUpgrade;

	class UpgradeManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param $instance IUpgradeManager
		 *
		 * @throws ContainerException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$upgrades = [
				MessageQueueUpgrade::class,
			];
			foreach ($upgrades as $upgrade) {
				$instance->registerUpgrade($this->container->create($upgrade, [], __METHOD__));
			}
		}
	}
