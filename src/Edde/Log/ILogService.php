<?php
	declare(strict_types=1);
	namespace Edde\Log;

	/**
	 * Implementation of a log service.
	 */
	interface ILogService extends ILog {
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
