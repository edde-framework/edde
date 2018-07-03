<?php
	declare(strict_types = 1);

	namespace Edde\Api\Log;

	/**
	 * Physical log storage (destination).
	 */
	interface ILog {
		/**
		 * shortcut for record();
		 *
		 * @param mixed $log
		 * @param array $tagList
		 *
		 * @return ILog
		 */
		public function log($log, array $tagList = null): ILog;

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
		 * @param array $tagList
		 *
		 * @return ILog
		 */
		public function info(string $log, array $tagList = null): ILog;

		/**
		 * adds warning tag
		 *
		 * @param string $log
		 * @param array $tagList
		 *
		 * @return ILog
		 */
		public function warning(string $log, array $tagList = null): ILog;

		/**
		 * adds error tag
		 *
		 * @param string $log
		 * @param array $tagList
		 *
		 * @return ILog
		 */
		public function error(string $log, array $tagList = null): ILog;

		/**
		 * adds critical tag
		 *
		 * @param string $log
		 * @param array $tagList
		 *
		 * @return ILog
		 */
		public function critical(string $log, array $tagList = null): ILog;

		/**
		 * log an exception
		 *
		 * @param \Exception $exception
		 * @param array|null $tagList
		 *
		 * @return ILog
		 */
		public function exception(\Exception $exception, array $tagList = null): ILog;
	}
