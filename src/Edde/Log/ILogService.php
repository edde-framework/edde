<?php
	declare(strict_types=1);
	namespace Edde\Log;

	use Edde\Filter\IFilter;

	/**
	 * Implementation of a log service.
	 */
	interface ILogService extends ILog {
		/**
		 * bind the given filter on the tag list; this can be useful for hiding/masking confidential data (passwords, ...)
		 *
		 * @param array   $tags
		 * @param IFilter $filter
		 *
		 * @return ILogService
		 */
		public function registerContentFilter(array $tags, IFilter $filter): ILogService;

		/**
		 * register the given log to the given set of tags
		 *
		 * @param ILog  $log
		 * @param array $tags
		 *
		 * @return ILogService
		 */
		public function registerLog(ILog $log, array $tags = null): ILogService;
	}
