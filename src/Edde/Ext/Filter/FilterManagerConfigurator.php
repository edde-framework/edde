<?php
	namespace Edde\Ext\Filter;

		use Edde\Api\Container\Exception\ContainerException;
		use Edde\Api\Container\Exception\FactoryException;
		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Filter\IFilterManager;
		use Edde\Common\Config\AbstractConfigurator;
		use Edde\Common\Filter\ToBoolFilter;

		class FilterManagerConfigurator extends AbstractConfigurator {
			use Container;

			/**
			 * @param IFilterManager $instance
			 *
			 * @throws ContainerException
			 * @throws FactoryException
			 */
			public function configure($instance) {
				parent::configure($instance);
				$instance->registerFilterList([
					'bool' => $this->container->create(ToBoolFilter::class, [], __METHOD__),
				]);
			}
		}
