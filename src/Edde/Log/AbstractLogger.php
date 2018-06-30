<?php
	declare(strict_types=1);
	namespace Edde\Log;

	use Edde\Edde;
	use Throwable;

	/**
	 * Common stuff for loggers.
	 */
	abstract class AbstractLogger extends Edde implements ILogger {
		/** @inheritdoc */
		public function log($log, array $tags = null): ILogger {
			return $this->record(new Log($log, $tags));
		}

		/** @inheritdoc */
		public function info(string $log, array $tags = null): ILogger {
			$tags[] = __FUNCTION__;
			return $this->log($log, $tags);
		}

		/** @inheritdoc */
		public function warning(string $log, array $tags = null): ILogger {
			$tags[] = __FUNCTION__;
			return $this->log($log, $tags);
		}

		/** @inheritdoc */
		public function error(string $log, array $tags = null): ILogger {
			$tags[] = __FUNCTION__;
			return $this->log($log, $tags);
		}

		/** @inheritdoc */
		public function critical(string $log, array $tags = null): ILogger {
			$tags[] = __FUNCTION__;
			return $this->log($log, $tags);
		}

		/** @inheritdoc */
		public function exception(Throwable $exception, array $tags = null): ILogger {
			$tags[] = __FUNCTION__;
			return $this->log($exception, $tags);
		}
	}
