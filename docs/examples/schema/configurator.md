# Configurator

**Related**: [Schema](/edde/schema), [Configurators](/edde/configurators)

When Schema Manager is used, it should know about all available schemas as in general there
are "few" of them.

> This step could be a bit less transparent or not so much convenient, but it's necessary to keep
the things clear as the user exactly knows which schemas are loaded and available; when used,
`ISchemaManager::getSchema()` method is called which throws an exception if schema is not loaded.
If you prefer some more magical approach, you can implement it in this loader.

?> **backend/src/Sandbox/Configurator/SchemaManagerConfigurator.php**

```php
<?php
	declare(strict_types=1);
	namespace Sandbox\Configurator;

	use Edde\Configurable\AbstractConfigurator;
	use Edde\Schema\ISchemaManager;
	use Edde\Schema\SchemaException;

	class SchemaManagerConfigurator extends AbstractConfigurator {
		/**
		 * @param $instance ISchemaManager
		 *
		 * @throws SchemaException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->loads([
				// place all your schemas here
			]);
		}
	}

```

**Previous**: [Index](/examples/schema/index) | **Next**: [Registration](/examples/schema/registration)
