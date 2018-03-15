<?php
	declare(strict_types=1);
	namespace Edde\Ext\Generator;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Generator\IGeneratorManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Generator\DateTimeGenerator;
	use Edde\Common\Generator\UuidGenerator;

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
			$instance->registerGeneratorList([
				'uuid'  => $this->container->create(UuidGenerator::class, [], __METHOD__),
				'stamp' => $this->container->create(DateTimeGenerator::class, [], __METHOD__),
			]);
		}
	}
