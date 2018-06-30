<?php
	declare(strict_types=1);
	namespace Edde\Log;

	use Edde\Configurable\AbstractConfigurator;

	class LogServiceConfigurator extends AbstractConfigurator {
		/**
		 * @param ILogService $instance
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->registerLogger(new StdLogger());
		}
	}
