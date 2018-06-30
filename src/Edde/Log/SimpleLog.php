<?php
	declare(strict_types=1);
	namespace Edde\Log;

	/**
	 * Do basically nothing but collecting log records; this should be used
	 * carefully as it could simply run out of memory.
	 */
	class SimpleLog extends AbstractLogger {
		/** @var ILog[] */
		protected $logs = [];

		/** @inheritdoc */
		public function record(ILog $log, array $tags = null): ILogger {
			$this->logs[] = $log;
			return $this;
		}

		public function getLogs(): array {
			return $this->logs;
		}
	}
