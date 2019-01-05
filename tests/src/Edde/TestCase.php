<?php
	declare(strict_types=1);
	namespace Edde;

	use Edde\Config\IConfigLoader;
	use Edde\Configurable\AbstractConfigurator;
	use Edde\Container\Autowire;
	use Edde\Container\ContainerException;
	use Edde\Container\ContainerFactory;
	use Edde\Container\IAutowire;
	use Edde\Factory\ClassFactory;
	use Edde\Service\Container\Container;
	use PHPUnit\Framework\TestCase as PhpUnitTestCase;

	abstract class TestCase extends PhpUnitTestCase implements IAutowire {
		use Autowire;
		use Container;

		/**
		 * @inheritdoc
		 *
		 * @throws ContainerException
		 */
		protected function setUp() {
			ContainerFactory::inject($this, [
				new ClassFactory(),
			], [
				IConfigLoader::class => new class() extends AbstractConfigurator {
					/**
					 * @param IConfigLoader $instance
					 */
					public function configure($instance) {
						parent::configure($instance);
						$instance->require(__DIR__ . '/../../../config.ini');
					}
				},
			]);
		}
	}
