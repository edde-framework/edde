<?php
	namespace Edde\Ext\Generator;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Generator\IGeneratorManager;
		use Edde\Common\Config\AbstractConfigurator;
		use Edde\Common\Generator\GuidGenerator;

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
					'guid' => $this->container->create(GuidGenerator::class, [], __METHOD__),
				]);
			}
		}
