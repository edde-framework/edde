<?php
	/**
	 * This script is responsible for container creation, thus this is kind of bootstrapper.
	 *
	 * There should not be any kind of "heavy" code, constants and other shits usually used in
	 * this type of file; main purpose is container configuration and creation, it's not necessary
	 * to do any other tasks here.
	 */
	declare(strict_types=1);
	use App\Common\Application\Context;
	use App\Common\Router\RouterServiceConfigurator;
	use Edde\Api\Application\IContext;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Container\Factory\CascadeFactory;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use Tracy\Debugger;

	/**
	 * All required dependencies here; to prevent "folder up jumps" in path, this file
	 * should see all other required loaders.
	 */
	require_once __DIR__ . '/lib/autoload.php';
	require_once __DIR__ . '/src/loader.php';
	/**
	 * showcase application loader; intentionally separated from main source
	 */
	require_once __DIR__ . '/src/App/loader.php';
	/**
	 * Tracy is a bit piece of shit, but quite useful; there is only problem with not so much
	 * transparent configuration through properties (this is the only example of acceptable
	 * scripted thing in this file).
	 */
	Debugger::enable(($isLocal = file_exists($local = __DIR__ . '/loader.local.php')) ? Debugger::DEVELOPMENT : Debugger::PRODUCTION, __DIR__ . '/logs');
	Debugger::$strictMode = true;
	Debugger::$showBar = $isLocal;
	Debugger::$onFatalError[] = function ($e) {
		Debugger::log($e);
	};

	/**
	 * Container factory is the simplest way how to create dependency container; in this particular case container is also
	 * configured to get "default" set of services defined in Edde.
	 *
	 * There is also option to create only container itself without any internal dependencies (not so much recommended except
	 * you are heavy masochist).
	 */
	try {
		$container = ContainerFactory::container($factoryList = array_merge([
			/**
			 * This application is using specific contexts to separate user experience
			 */
			IContext::class => Context::class,
		], is_array($local = @include $local) ? $local : [], [
			/**
			 * This stranger here must (should be) be last, because it's canHandle method is able to kill a lot of dependencies and
			 * create not so much nice surprises. Thus, it must be last as kind of dependency fallback.
			 */
			new ClassFactory(),
		]), [
			IRouterService::class => RouterServiceConfigurator::class,
		]);
		/**
		 * This one is one of the most magical: this factory uses IContext::cascade() to search for class; this is quite
		 * epic feature, but also less transparent, useful to seamlessly switch context.
		 *
		 * Analyze every factory for dependencies is quite expensive task, so this feature has been removed. Becuase of that it's
		 * necessary to register cascade factory by container as it has some dependencies.
		 */
		$container->registerFactory($container->create(CascadeFactory::class, [], __FILE__));
		$container->create('run');
	} catch (\Throwable $e) {
		Debugger::log($e);
		die(sprintf('Critical application Exception [%s]; see logs.', get_class($e)));
	}
