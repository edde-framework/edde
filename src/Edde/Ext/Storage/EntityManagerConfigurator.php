<?php
	namespace Edde\Ext\Storage;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Storage\IEntityManager;
		use Edde\Common\Config\AbstractConfigurator;
		use Edde\Common\Filter\GuidFilter;

		class EntityManagerConfigurator extends AbstractConfigurator {
			use Container;

			/**
			 * @param IEntityManager $instance
			 *
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			public function configure($instance) {
				parent::configure($instance);
				$instance->registerGeneratorList([
					'guid' => $this->container->create(GuidFilter::class, [], __METHOD__),
				]);
			}
		}
