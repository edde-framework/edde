# Backend 

It's time to prepare mandatory stuff on backend.

!> From this point setup will be more Edde specific; you could (and should :wink:) continue, but if
you are interested just in the Container stuff, here it's enough; you will need a bit more to
be able to run the container, so continue reading.

## config.ini.template

There is a little magic translating environment variables into this file which is quite useful
as it's much more reliable than to depend on env.

> Config assumes you are using default Postgres database.

?> **backend/config.ini.template**

```ini
[postgres]
dsn = "pgsql:dbname=sandbox;user=${SANDBOX_DATABASE_USER};host=${SANDBOX_DATABASE_HOST};port=5432"
user = "${SANDBOX_DATABASE_USER}"
password = "${SANDBOX_DATABASE_PASSWORD}"
```

## index.php

File responsible for handling incoming http requests; everything is routed into one application to keep
things simple.

?> **backend/index.php**

```php
<?php
	declare(strict_types=1);

    require_once __DIR__ . '/runtime.php';
```

## cli

When you need to execute something on cli.

?> **backend/cli**

```php
#!/usr/bin/env php
<?php
	declare(strict_types=1);
	require_once __DIR__ . '/runtime.php';
```

## runtime.php

This file is responsible for actual application execution.

?> **backend/runtime.php**

```php
<?php
	declare(strict_types=1);
	use Edde\Application\IApplication;
	use Edde\Container\IContainer;

	// loader should create container instance (without any side effects)
	/** @var $container IContainer */
	$container = require_once __DIR__ . '/loader.php';
	// Edde specifies simple interface for an application lifecycle; exit is here to
	// report exit status of CLI applications (http don't care)
	exit($container->create(IApplication::class)->run());
```

## loader.php

Probably most important file composing parts of you application together; other frameworks are using different approach of
`Container` configuration, Edde is trying to keep as close to PHP as possible, thus whole configuration is done here and
programmatically.

?> **backend/loader.php**

```php
<?php
	declare(strict_types=1);
	use Edde\Config\IConfigLoader;
	use Edde\Configurable\AbstractConfigurator;
	use Edde\Container\ContainerFactory;
	use Edde\Factory\CascadeFactory;
	use Edde\Factory\ClassFactory;

	// load composer dependencies
	require_once __DIR__ . '/vendor/autoload.php';
	// prepare autoloader of you application
	require_once __DIR__ . '/src/loader.php';
	
	/**
	 * Container factory is the simplest way how to create dependency container; in this particular case container is also
	 * configured to get "default" set of services defined in Edde.
	 *
	 * There is also option to create only container itself without any internal dependencies (not so much recommended except
	 * you are heavy masochist).
	 */
	return ContainerFactory::container([
		new CascadeFactory(
			[
				// here you should you you root namespace; you can keep Edde here if you want to get
				// some support from the framework 
				'Sandbox',
				'Edde',
			]),
		/**
		 * This stranger here must (should be) be last, because it's canHandle method is able to kill a lot of dependencies and
		 * create not so much nice surprises. Thus, it must be last as kind of dependency fallback.
		 */
		new ClassFactory(),
	], [
		/**
		 * if you remember something about config.ini.template file, here we will prepare it for usage; configurator is responsible
         * for setting up class it's bound to (in this case ConfigLoader)
		 *
		 * it's quite hacky way, how to do this, but it's because of config file name specification and to keep things simpler 
		 */
		IConfigLoader::class   => new class() extends AbstractConfigurator {
			/**
			 * @param $instance IConfigLoader
			 */
			public function configure($instance) {
				parent::configure($instance);
				$instance->require(__DIR__ . '/config.ini');
			}
		},
	]);
```

## src/loader.php

If you will follow some simple rules, it's enough to have one class loader in an application.

> This file looks quite unnecessary, but it's better to have `require_once __DIR__ . '/src/loader.php';` than long jump into 
`require_once __DIR__ . '/src/Sandbox/loader.php';`. But it's just a convention. 

?> **backend/src/loader.php**

```php
<?php
	declare(strict_types=1);
	require_once __DIR__ . '/Sandbox/loader.php';
```

## src/Sandbox/loader.php

Real loader; this way is used to prevent deep jumps in directory structure even it's a bit "more" files.

?> **backend/src/Sandbox/loader.php**

```php
<?php
	declare(strict_types=1);
	namespace Sandbox;

	use Edde\Autoloader;

	Autoloader::register(__NAMESPACE__, __DIR__);
```

**Previous**: [Bin](/sandbox/bin) | **Next**: [Startup](/sandbox/startup)
