<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Filter;

	use DateTime;
	use Edde\Common\Filter\BoolFilter;
	use Edde\Common\Filter\DateTimeFilter;
	use Edde\Common\Filter\FloatFilter;
	use Edde\Common\Filter\IntFilter;
	use Edde\Config\AbstractConfigurator;
	use Edde\Filter\IFilterManager;
	use Edde\Inject\Container\Container;

	class FilterManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param \Edde\Filter\IFilterManager $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerFilters([
				'bool'          => $filter = $this->container->create(BoolFilter::class, [], __METHOD__),
				'boolean'       => $filter,
				'int'           => $this->container->create(IntFilter::class, [], __METHOD__),
				'float'         => $filter = $this->container->create(FloatFilter::class, [], __METHOD__),
				'double'        => $filter,
				DateTime::class => $filter = $this->container->create(DateTimeFilter::class, [], __METHOD__),
				'datetime'      => $filter,
			]);
		}
	}
