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
				'bool-int' => $this->container->create(BoolIntFilter::class, [], __METHOD__),
				'bool'     => $this->container->create(BoolFilter::class, [], __METHOD__),
			]);
//			$instance->registerGroup('storage', [
//			]);
		}
	}
