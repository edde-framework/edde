<?php
	declare(strict_types=1);
	namespace Edde\Api\Log;

	use Edde\Api\Config\IConfigurable;
	use Throwable;

	/**
	 * Physical log storage (destination).
	 */
	interface ILog extends IConfigurable {
		/**
		 * shortcut for record();
		 *
		 * @param mixed $log
		 * @param array $tags
		 *
		 * @return ILog
		 */
		public function log($log, array $tags = null): ILog;

		/**
		 * @param ILogRecord $logRecord
		 *
		 * @return ILog
		 */
		public function record(ILogRecord $logRecord): ILog;

		/**
		 * adds informative tag
		 *
		 * @param string $log
		 * @param array  $tags
		 *
		 * @return ILog
		 */
		public function info(string $log, array $tags = null): ILog;

		/**
		 * adds warning tag
		 *
		 * @param string $log
		 * @param array  $tags
		 *
		 * @return ILog
		 */
		public function warning(string $log, array $tags = null): ILog;

		/**
		 * adds error tag
		 *
		 * @param string $log
		 * @param array  $tags
		 *
		 * @return ILog
		 */
		public function error(string $log, array $tags = null): ILog;

		/**
		 * adds critical tag
		 *
		 * @param string $log
		 * @param array  $tags
		 *
		 * @return ILog
		 */
		public function critical(string $log, array $tags = null): ILog;

		/**
		 * log an exception
		 *
		 * @param Throwable  $exception
		 * @param array|null $tags
		 *
		 * @return ILog
		 */
		public function exception(Throwable $exception, array $tags = null): ILog;
	}
