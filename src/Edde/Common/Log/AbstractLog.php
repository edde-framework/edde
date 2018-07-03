<?php
	declare(strict_types=1);

	namespace Edde\Common\Log;

	use Edde\Api\Log\ILog;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	/**
	 * Common stuff for loggers.
	 */
	abstract class AbstractLog extends Object implements ILog {
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function info(string $log, array $tagList = null): ILog {
			$tagList[] = __FUNCTION__;
			return $this->log($log, $tagList);
		}

		/**
		 * @inheritdoc
		 */
		public function log($log, array $tagList = null): ILog {
			return $this->record(new LogRecord($log, $tagList));
		}

		/**
		 * @inheritdoc
		 */
		public function warning(string $log, array $tagList = null): ILog {
			$tagList[] = __FUNCTION__;
			return $this->log($log, $tagList);
		}

		/**
		 * @inheritdoc
		 */
		public function error(string $log, array $tagList = null): ILog {
			$tagList[] = __FUNCTION__;
			return $this->log($log, $tagList);
		}

		/**
		 * @inheritdoc
		 */
		public function critical(string $log, array $tagList = null): ILog {
			$tagList[] = __FUNCTION__;
			return $this->log($log, $tagList);
		}

		/**
		 * @inheritdoc
		 */
		public function exception(\Throwable $exception, array $tagList = null): ILog {
			$tagList[] = __FUNCTION__;
			return $this->log($exception, $tagList);
		}
	}
