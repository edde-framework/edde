<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Filter;

	use DateTime;
	use Edde\Config\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Filter\BoolFilter;
	use Edde\Filter\DateTimeFilter;
	use Edde\Filter\FloatFilter;
	use Edde\Filter\IFilterManager;
	use Edde\Filter\IntFilter;
	use Edde\Filter\JsonFilter;
	use Edde\Service\Container\Container;

	class FilterManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param IFilterManager $instance
		 *
		 * @throws ContainerException
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
				'json'          => $this->container->create(JsonFilter::class, [], __METHOD__),
			]);
		}
	}
