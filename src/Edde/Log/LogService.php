<?php
	declare(strict_types=1);
	namespace Edde\Log;

	use function array_unique;

	/**
	 * Default implementation of log service.
	 */
	class LogService extends AbstractLogger implements ILogService {
		/** @var ILogger[] */
		protected $loggers = [];

		/** @inheritdoc */
		public function registerLogger(ILogger $logger): ILogService {
			$this->loggers[] = $logger;
			return $this;
		}

		/** @inheritdoc */
		public function stdout(string $log, array $tags = []): ILogService {
			$tags[] = __FUNCTION__;
			$this->log($log, $tags);
			return $this;
		}

		/** @inheritdoc */
		public function stderr(string $log, array $tags = []): ILogService {
			$tags[] = __FUNCTION__;
			$this->log($log, $tags);
			return $this;
		}

		/** @inheritdoc */
		public function record(ILog $log, array $tags = []): void {
			$tags = array_unique($tags ?: []);
			foreach ($this->loggers as $logger) {
				$logger->record($log, $tags);
			}
		}
	}
