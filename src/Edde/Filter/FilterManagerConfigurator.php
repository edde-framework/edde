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
				'bool-int'         => $filter = new BoolIntFilter(),
				'storage:bool'     => $filter,
				'storage:stamp'    => $filter = new StampFilter(),
				'bool'             => $filter = new BoolFilter(),
				'uuid'             => $filter = $this->container->create(UuidFilter::class, [], __METHOD__),
				'storage:uuid'     => $filter,
				'datetime'         => $filter = new DateTimeFilter(),
				'storage:DateTime' => $filter,
				'string'           => $filter = new StampFilter(),
				'storage:string'   => $filter,
				'float'            => $filter = new FloatFilter(),
				'storage:float'    => $filter,
			]);
		}
	}
