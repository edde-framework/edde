# Startup

Now everything is prepared for execution, it's time to say hello to you new and shiny container.

## Startup

Use `./bin/local.sh` to startup your container; it will take some time when everything is done, you will be in your
container.

```
# Starting point where you can start to do any magic you want: 
/sandbox/backend #
```

## Prepare composer

Composer is available from Docker image, so you can start your project by `composer.json` creation followed by dependencies. 

```
# init composer, follow instructions on screen 
/sandbox/backend # composer init
```

> You should have a look into `composer.json` as it will probably need some adjustments. 

## Edde Time

Because all of this you are doing because you want to use Edde, it's good to install it :wink: 

```
# init composer, follow instructions on screen 
/sandbox/backend # composer require edde-framework/edde
```

!> Be careful as this will install `master` version of Edde, thus latest version; in general it's quite safe to use it, but it's better
to stick to a particular version.

## First Controller

It would be nice after all to see, if the things is working, so let's create http controller. More about the stuff [here](/edde/controllers).

?> **backend/src/Sandbox/Http/Hello/WorldController.php**

```php
<?php
	declare(strict_types=1);
	namespace Sandbox\Http\Hello;

	use Edde\Controller\HttpController;

	class WorldController extends HttpController {
		public function actionCheers() {
			$this->textResponse('cheers!')->execute();
		}
	}	
```

Go to browser on `http://{localhost | docker-ip}:2680/hello.world/cheers` and you'll get `cheers!`.

**Previous**: [Backend](/sandbox/backend)
