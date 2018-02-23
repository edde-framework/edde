<?php
	declare(strict_types=1);
	namespace Edde\Ext\Filter;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Filter\IFilterManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Filter\BoolFilter;
	use Edde\Common\Filter\DateTimeFilter;
	use Edde\Common\Filter\FloatFilter;

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
			$instance->registerFilters([
				'bool'           => $filter = $this->container->create(BoolFilter::class, [], __METHOD__),
				'boolean'        => $filter,
				'float'          => $filter = $this->container->create(FloatFilter::class, [], __METHOD__),
				'double'         => $filter,
				\DateTime::class => $filter = $this->container->create(DateTimeFilter::class, [], __METHOD__),
				'datetime'       => $filter,
			]);
		}
	}
