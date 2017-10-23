<?php
	namespace Edde\Ext\Filter;

		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Filter\IFilterManager;
		use Edde\Common\Config\AbstractConfigurator;

		class FilterManagerConfigurator extends AbstractConfigurator {
			use Container;

			/**
			 * @param IFilterManager $instance
			 */
			public function configure($instance) {
				parent::configure($instance);
			}
		}
