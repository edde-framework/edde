<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Filter;

	use DateTime;
	use Edde\Config\AbstractConfigurator;
	use Edde\Inject\Container\Container;

	class FilterManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param \Edde\Filter\IFilterManager $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerFilters([
				'bool'          => $filter = $this->container->create(\Edde\Filter\BoolFilter::class, [], __METHOD__),
				'boolean'       => $filter,
				'int'           => $this->container->create(\Edde\Filter\IntFilter::class, [], __METHOD__),
				'float'         => $filter = $this->container->create(\Edde\Filter\FloatFilter::class, [], __METHOD__),
				'double'        => $filter,
				DateTime::class => $filter = $this->container->create(\Edde\Filter\DateTimeFilter::class, [], __METHOD__),
				'datetime'      => $filter,
			]);
		}
	}
