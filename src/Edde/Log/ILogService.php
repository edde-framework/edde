<?php
	declare(strict_types=1);
	namespace Edde\Log;

	/**
	 * Implementation of a log service.
	 */
	interface ILogService extends ILogger {
		/**
		 * register the given log to the given set of tags
		 *
		 * @param ILogger $logger
		 *
		 * @return ILogService
		 */
		public function registerLogger(ILogger $logger): ILogService;
	}
