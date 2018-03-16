<?php
	declare(strict_types=1);
	namespace Edde\Ext\Generator;

	use Edde\Api\Generator\IGeneratorManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Generator\DateTimeGenerator;
	use Edde\Common\Generator\UuidGenerator;
	use Edde\Exception\Container\ContainerException;
	use Edde\Exception\Container\FactoryException;
	use Edde\Inject\Container\Container;

	class GeneratorManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IGeneratorManager $instance
		 *
		 * @throws ContainerException
		 * @throws FactoryException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerGenerators([
				'uuid'  => $this->container->create(UuidGenerator::class, [], __METHOD__),
				'stamp' => $this->container->create(DateTimeGenerator::class, [], __METHOD__),
			]);
		}
	}
