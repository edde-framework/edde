<?php
	declare(strict_types=1);
	namespace Edde\Common\Log;

	use Edde\Api\Log\ILog;
	use Edde\Object;
	use Throwable;

	/**
	 * Common stuff for loggers.
	 */
	abstract class AbstractLog extends Object implements ILog {
		/** @inheritdoc */
		public function info(string $log, array $tags = null): ILog {
			$tags[] = __FUNCTION__;
			return $this->log($log, $tags);
		}

		/** @inheritdoc */
		public function log($log, array $tags = null): ILog {
			return $this->record(new LogRecord($log, $tags));
		}

		/** @inheritdoc */
		public function warning(string $log, array $tags = null): ILog {
			$tags[] = __FUNCTION__;
			return $this->log($log, $tags);
		}

		/** @inheritdoc */
		public function error(string $log, array $tags = null): ILog {
			$tags[] = __FUNCTION__;
			return $this->log($log, $tags);
		}

		/** @inheritdoc */
		public function critical(string $log, array $tags = null): ILog {
			$tags[] = __FUNCTION__;
			return $this->log($log, $tags);
		}

		/** @inheritdoc */
		public function exception(Throwable $exception, array $tags = null): ILog {
			$tags[] = __FUNCTION__;
			return $this->log($exception, $tags);
		}
	}
