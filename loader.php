<?php
	declare(strict_types=1);
	use Edde\Config\IConfigLoader;
	use Edde\Configurable\AbstractConfigurator;
	use Edde\Container\ContainerFactory;
	use Edde\Factory\CascadeFactory;
	use Edde\Factory\ClassFactory;
	use Edde\Upgrade\IUpgradeManager;
	use Edde\Upgrade\UpgradeManagerConfigurator;

	require_once __DIR__ . '/lib/autoload.php';
	require_once __DIR__ . '/src/loader.php';
	return ContainerFactory::container([
		new CascadeFactory(
			[
				'Edde',
			]),
		/**
		 * This stranger here must (should be) be last, because it's canHandle method is able to kill a lot of dependencies and
		 * create not so much nice surprises. Thus, it must be last as kind of dependency fallback.
		 */
		new ClassFactory(),
	], [
		IUpgradeManager::class => UpgradeManagerConfigurator::class,
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
