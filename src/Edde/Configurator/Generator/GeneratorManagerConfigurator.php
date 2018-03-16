<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Generator;

	use Edde\Config\AbstractConfigurator;
	use Edde\Generator\IGeneratorManager;
	use Edde\Inject\Container\Container;

	class GeneratorManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IGeneratorManager $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerGenerators([
				'uuid'  => $this->container->create(\Edde\Generator\UuidGenerator::class, [], __METHOD__),
				'stamp' => $this->container->create(\Edde\Generator\DateTimeGenerator::class, [], __METHOD__),
			]);
		}
	}
