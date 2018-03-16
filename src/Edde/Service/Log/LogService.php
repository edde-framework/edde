<?php
	declare(strict_types=1);
	namespace Edde\Service\Log;

	use Edde\Filter\IFilter;
	use Edde\Log\AbstractLog;
	use Edde\Log\ILog;
	use Edde\Log\ILogService;

	/**
	 * Default implementation of log service.
	 */
	class LogService extends AbstractLog implements ILogService {
		/** @var \Edde\Filter\IFilter[] */
		protected $contentFilters = [];
		/** @var ILog[] */
		protected $logs = [];

		/**
		 * @inheritdoc
		 */
		public function registerContentFilter(array $tags, IFilter $filter): ILogService {
			foreach ($tags as $tag) {
				$this->contentFilters[$tag] = $filter;
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerLog(ILog $log, array $tags = null): ILogService {
			$tags = $tags ?: [null];
			foreach ($tags as $tag) {
				$this->logs[$tag] = $log;
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function record(\Edde\Log\ILogRecord $logRecord): ILog {
			$tags = array_unique(($tags = $logRecord->getTags()) ? $tags : [null]);
			if (empty($this->contentFilters) !== true) {
				$log = $logRecord->getLog();
				foreach ($tags as $tag) {
					$log = isset($this->contentFilters[$tag]) ? $this->contentFilters[$tag]->filter($log) : $log;
				}
			}
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
