# Getting Started

## Before start

?> It's good to get acquainted with [concepts and ideas](/ideas.md) behind the framework to prevent some surprises.

Edde does not enforce any directory structure unlike others do; in general it's recommended
to put you sources into `src` dir and tests to `tests` directory. Your application is basically
kind of "library" put inside some "blackbox" (in this case Edde); it's not necessary to mess
with complex directory structure.

## Installation

Use [Composer](https://getcomposer.org/doc/00-intro.md) to install the stuff.

`$ composer install edde-framework/edde`

It's recommended to create a few files in you project:

### index.php

Obviously getting requests from a webserver.

```php
<?php
	declare(strict_types=1);
	require_once __DIR__ . '/runtime.php';

```

### cli

File without any extension; this will be for calling CLI commands in you application. 

```php
#!/usr/bin/env php
<?php
	declare(strict_types=1);
	require_once __DIR__ . '/runtime.php';

```

### runtime.php

This file will execute you application (regardless of cli or http request, everything is routed into
one application). 

```php
<?php
	declare(strict_types=1);
	use Edde\Application\IApplication;
	use Edde\Container\IContainer;

	/** @var $container IContainer */
	$container = require_once __DIR__ . '/loader.php';
	exit($container->create(IApplication::class)->run());

```

### loader.php

Magical file which will configure `Container` and whole piece of your application (kind of config);
there are no other magical places for configuration (except of optional simple scalar ini config).

This file is standalone as it only creates `Container` which could be reused by any other script,
thus sharing one application context (it's not necessary to build a lot of configs per cli/http/tests,...).

```php
<?php
	declare(strict_types=1);
	/**
	 * All required dependencies here; to prevent "folder up jumps" in path, this file
	 * should see all other required loaders.
	 */
	use Edde\Config\IConfigLoader;
	use Edde\Configurable\AbstractConfigurator;
	use Edde\Container\ContainerFactory;
	use Edde\Factory\CascadeFactory;
	use Edde\Factory\ClassFactory;

	require_once __DIR__ . '/vendor/autoload.php';
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
				/** your root namespace name here */,
				'Edde',
			]),
		/**
		 * This stranger here must (should be) be last, because it's canHandle method is able to kill a lot of dependencies and
		 * create not so much nice surprises. Thus, it must be last as kind of dependency fallback.
		 */
		new ClassFactory(),
	], [
		/**
		 * this is a bit "illegal" way, how to do configuration, but it... works
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

### config.ini.template

Almost every application needs some scalar configuration (database, cache, ...); this file should be used in conjunction
with environment parameters (like in this example) to keep one source of configuration (this ini is much more reliable
than environment variables).

```ini
[postgres]
dsn = "pgsql:dbname={DATABASE_NAME};user=${DATABASE_USER};host=${DATABASE_HOST};port=5432"
user = "${DATABASE_USER}"
password = "${DATABASE_PASSWORD}"
```

## First steps

### Docker

It's strongly recommended to start with a Docker image, you can simply build you own, some docs about it [here](/docker.md).

### Source directory

It's time to create http controller to handle incoming requests. More how this magic works [here](/edde/controllers.md).

?> src\Fooplication\Http\Hello\HelloController.php 

```php
<?php
	declare(strict_types=1);
	namespace Fooplication\Http\Hello;
	
	use Edde\Controller\HttpController;
	
	class HelloController extends HttpController {
		public function actionHelloWorld() {
			$this->textResponse('hello there!')->execute();
		}
	}
```

Thing will be available on:
`http://<localhost>/hello.hello/hello-world`
