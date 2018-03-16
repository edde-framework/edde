<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Generator;

	use Edde\Common\Generator\DateTimeGenerator;
	use Edde\Common\Generator\UuidGenerator;
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
				'uuid'  => $this->container->create(UuidGenerator::class, [], __METHOD__),
				'stamp' => $this->container->create(DateTimeGenerator::class, [], __METHOD__),
			]);
		}
	}
