# Registration

`UpgradeManager` is done, Configurator is done, now it's time to register the stuff into application. 

> `loader.php` is shortened just for this example 

?> **backend/loader.php**

```php
<?php
	declare(strict_types=1);
	
	use Edde\Container\ContainerFactory;
	use Edde\Upgrade\IUpgradeManager;
	use Sandbox\Configurators\UpgradeManagerConfigurator;
	use Sandbox\Upgrade\UpgradeManager;
	
	// ... shortened...
	
	return ContainerFactory::container([
		/** interface => implementation bindings... */
		// ... 
		/** register you new nice and shiny upgrade manager; it should be somewhere at the beginning */
		IUpgradeManager::class => UpgradeManager::class,
		// ... 
	], [
		/** configurator bindings */
		// ...
		/**
         * register configurator for your upgrade manager; when somebody touch it, configurator will be executed and prepare
         * upgrade manager for use  
         */
		IUpgradeManager::class => UpgradeManagerConfigurator::class,
		// ...
	]);
```

**Previous**: [Configurator](/examples/upgrades/configurator) | **Next**: [Upgrade](/examples/upgrades/upgrade)
