<?php
	declare(strict_types=1);
	namespace Edde;

	use Edde\Assets\IRootDirectory;
	use Edde\Config\AbstractConfigurator;
	use Edde\Config\IConfigLoader;
	use Edde\Container\ContainerException;
	use Edde\Container\ContainerFactory;
	use Edde\Container\Factory\ClassFactory;
	use Edde\Container\IAutowire;
	use Edde\Inject\Assets\RootDirectory;
	use Edde\Inject\Container\Container;
	use PHPUnit\Framework\TestCase as PhpUnitTestCase;
	use ReflectionException;

	abstract class TestCase extends PhpUnitTestCase implements IAutowire {
		use Autowire;
		use Container;

		/**
		 * @inheritdoc
		 *
		 * @throws ContainerException
		 * @throws ReflectionException
		 */
		protected function setUp() {
			ContainerFactory::inject($this, [
				IRootDirectory::class => ContainerFactory::instance(RootDirectory::class, [__DIR__ . '/../..']),
				new ClassFactory(),
			], [
				IConfigLoader::class => new class() extends AbstractConfigurator {
					/**
					 * @param IConfigLoader $instance
					 */
					public function configure($instance) {
						parent::configure($instance);
						$instance->require(__DIR__ . '/../config.ini');
					}
				},
			]);
		}
	}
