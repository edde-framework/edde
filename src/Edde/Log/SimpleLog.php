<?php
	declare(strict_types=1);
	namespace Edde\Log;

	/**
	 * Do basically nothing but collecting log records; this should be used
	 * carefully as it could simply run out of memory.
	 */
	class SimpleLog extends AbstractLog {
		/** @var ILogRecord[] */
		protected $logRecords = [];

		/** @inheritdoc */
		public function record(ILogRecord $logRecord): ILog {
			$this->logRecords[] = $logRecord;
			return $this;
		}

		public function getLogRecords(): array {
			return $this->logRecords;
		}
	}
