# Registration

Configurator must be bound to it's service. 

> `loader.php` is shortened just for this example 

?> **backend/loader.php**

```php
<?php
	declare(strict_types=1);
	
	use Edde\Container\ContainerFactory;
	use Edde\Schema\ISchemaManager;
	use Sandbox\Configurator\SchemaManagerConfigurator;
	
	// ... shortened...
	
	return ContainerFactory::container([
		// ...
		// service bindings
		// ... 
	], [
		/** configurator bindings */
		// ...
		/**
         * register configurator for your upgrade manager; when somebody touch it, configurator will be executed and prepare
         * upgrade manager for use  
         */
		ISchemaManager::class => SchemaManagerConfigurator::class,
		// ...
	]);
```

> **That's it!** When you use `SchemaManager` it will be configured by your class and all schemas will be available 
for usage.

**Previous**: [Configurator](/examples/schema/configurator)
