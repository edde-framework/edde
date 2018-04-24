<?php
	declare(strict_types=1);
	namespace Edde\Log;

	/**
	 * Default implementation of log service.
	 */
	class LogService extends AbstractLog implements ILogService {
		/** @var ILog[] */
		protected $logs = [];

		/** @inheritdoc */
		public function registerLog(ILog $log, array $tags = null): ILogService {
			$tags = $tags ?: [null];
			foreach ($tags as $tag) {
				$this->logs[$tag] = $log;
			}
			return $this;
		}

		/** @inheritdoc */
		public function record(ILogRecord $logRecord): ILog {
			$tags = array_unique(($tags = $logRecord->getTags()) ? $tags : [null]);
			/**
			 * second run because all filter has been applied
			 */
			foreach ($tags as $tag) {
				if (isset($this->logs[$tag]) === false) {
					continue;
				}
				$log = $this->logs[$tag];
				$log->setup();
				$log->record($logRecord);
			}
			return $this;
		}
	}
