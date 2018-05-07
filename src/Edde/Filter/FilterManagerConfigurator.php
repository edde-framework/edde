<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Config\AbstractConfigurator;
	use Edde\Container\ContainerException;
	use Edde\Service\Container\Container;

	class FilterManagerConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param $instance IFilterManager
		 *
		 * @throws ContainerException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerFilters([
				'bool-int'         => $filter = $this->container->create(BoolIntFilter::class, [], __METHOD__),
				'storage:bool'     => $filter,
				'storage:stamp'    => $filter = $this->container->create(StampFilter::class, [], __METHOD__),
				'bool'             => $filter = $this->container->create(BoolFilter::class, [], __METHOD__),
				'uuid'             => $filter = $this->container->create(UuidFilter::class, [], __METHOD__),
				'storage:uuid'     => $filter,
				'datetime'         => $filter = $this->container->create(DateTimeFilter::class, [], __METHOD__),
				'storage:DateTime' => $filter,
			]);
		}
	}
