<?php
	declare(strict_types=1);

	namespace Edde\Ext\Log;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Log\ILogService;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Log\FileLog;

	class LogServiceConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param ILogService $instance
		 */
		public function configure($instance) {
			$instance->registerLog($this->container->create(FileLog::class, ['default'], __METHOD__), [
				'info',
				'error',
				'warning',
				'critical',
			]);
		}
	}
