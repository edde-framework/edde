<?php
	declare(strict_types=1);
	namespace Edde;

	use Edde\Api\Config\IConfigLoader;
	use Edde\Api\Container\IAutowire;
	use Edde\Assets\IRootDirectory;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Container\AutowireTrait;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Container\FactoryException;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Inject\Assets\RootDirectory;
	use PHPUnit\Framework\TestCase as PhpUnitTestCase;
	use ReflectionException;

	abstract class TestCase extends PhpUnitTestCase implements IAutowire {
		use AutowireTrait;

		/**
		 * @throws ContainerException
		 * @throws FactoryException
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
