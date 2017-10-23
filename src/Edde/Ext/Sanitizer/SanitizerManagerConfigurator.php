<?php
	namespace Edde\Ext\Sanitizer;

		use Edde\Api\Container\Inject\Container;
		use Edde\Api\Sanitizer\ISanitizerManager;
		use Edde\Common\Config\AbstractConfigurator;

		class SanitizerManagerConfigurator extends AbstractConfigurator {
			use Container;

			/**
			 * @param ISanitizerManager $instance
			 */
			public function configure($instance) {
				parent::configure($instance);
			}
		}
