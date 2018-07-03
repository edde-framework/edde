<?php
	declare(strict_types=1);

	namespace Edde\Ext\Log;

	use Edde\Api\Container\Exception\ContainerException;
	use Edde\Api\Container\Exception\FactoryException;
	use Edde\Api\Container\Inject\Container;
	use Edde\Api\Log\ILogService;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Log\FileLog;

	class LogServiceConfigurator extends AbstractConfigurator {
		use Container;

		/**
		 * @param ILogService $instance
		 *
		 * @throws ContainerException
		 * @throws FactoryException
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
