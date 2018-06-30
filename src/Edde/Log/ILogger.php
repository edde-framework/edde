<?php
	declare(strict_types=1);
	namespace Edde\Log;

	use Edde\Configurable\IConfigurable;
	use Throwable;

	/**
	 * Target of log item
	 */
	interface ILogger extends IConfigurable {
		/**
		 * shortcut for record();
		 *
		 * @param mixed $log
		 * @param array $tags
		 *
		 * @return ILogger
		 */
		public function log($log, array $tags = null): ILogger;

		/**
		 * @param ILog       $log
		 * @param array|null $tags
		 *
		 * @return ILogger
		 */
		public function record(ILog $log, array $tags = null): ILogger;

		/**
		 * adds informative tag
		 *
		 * @param string $log
		 * @param array  $tags
		 *
		 * @return ILogger
		 */
		public function info(string $log, array $tags = null): ILogger;

		/**
		 * adds warning tag
		 *
		 * @param string $log
		 * @param array  $tags
		 *
		 * @return ILogger
		 */
		public function warning(string $log, array $tags = null): ILogger;

		/**
		 * adds error tag
		 *
		 * @param string $log
		 * @param array  $tags
		 *
		 * @return ILogger
		 */
		public function error(string $log, array $tags = null): ILogger;

		/**
		 * adds critical tag
		 *
		 * @param string $log
		 * @param array  $tags
		 *
		 * @return ILogger
		 */
		public function critical(string $log, array $tags = null): ILogger;

		/**
		 * log an exception
		 *
		 * @param Throwable  $exception
		 * @param array|null $tags
		 *
		 * @return ILogger
		 */
		public function exception(Throwable $exception, array $tags = null): ILogger;
	}
