<?php
	declare(strict_types=1);
	namespace Edde\Filter;

	use Edde\Configurable\AbstractConfigurator;
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
				'uuid'             => $filter = $this->container->inject(new UuidFilter()),
				'storage:uuid'     => $filter,
				'datetime'         => $filter = new DateTimeFilter(),
				'DateTime'         => $filter,
				'storage:DateTime' => $filter,
				'string'           => $filter = new StringFilter(),
				'storage:string'   => $filter,
				'float'            => $filter = new FloatFilter(),
				'storage:float'    => $filter,
				'int'              => $filter = new IntFilter(),
				'storage:int'      => $filter,
				'storage:json'     => new JsonFilter(),
				'binary-uuid'      => new BinaryUuidFilter(),
			]);
		}
	}
