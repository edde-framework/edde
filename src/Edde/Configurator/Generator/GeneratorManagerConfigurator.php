<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Generator;

	use Edde\Config\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Generator\DateTimeGenerator;
	use Edde\Generator\IGeneratorManager;
	use Edde\Generator\UuidGenerator;
	use Edde\Inject\Container\Container;

	class GeneratorManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IGeneratorManager $instance
		 *
		 * @throws ContainerException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerGenerators([
				'uuid'  => $this->container->create(UuidGenerator::class, [], __METHOD__),
				'stamp' => $this->container->create(DateTimeGenerator::class, [], __METHOD__),
			]);
		}
	}
