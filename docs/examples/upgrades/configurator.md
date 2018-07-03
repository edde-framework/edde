# Configurator

**Related**: [Configurators](/components/configurators)

This is a bit magical part: Create a Configurator responsible for setting up `UpgradeManager` you just created and register it for setup.

?> **backend/src/Sandbox/Configurator/UpgradeManagerConfigurator.php**

```php
<?php
	declare(strict_types=1);
	namespace Sandbox\Configurator;
	
	use Edde\Configurable\AbstractConfigurator;
	use Edde\Service\Container\Container;
	use Edde\Upgrade\IUpgradeManager;
	
	class UpgradeManagerConfigurator extends AbstractConfigurator {
		use Container;
		
		/** @var $instance IUpgradeManager */
		public function configure($instance){
			/** it's good practice to leave parent configurator here */
            parent::configure($instance);
			$upgrades = [
				// ordered list of your upgrades; just can grow, NEVER change order of upgrades
			];
			foreach ($upgrades as $upgrade) {
				$instance->registerUpgrade($this->container->create($upgrade, [], __METHOD__));
			}
		}
	}
```

**Previous**: [Upgrade Manager](/examples/upgrades/upgrade-manager) | **Next**: [Registration](/examples/upgrades/registration)
