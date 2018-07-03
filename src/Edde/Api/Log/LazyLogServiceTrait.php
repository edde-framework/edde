<?php
	declare(strict_types = 1);

	namespace Edde\Api\Log;

	/**
	 * Lazy log service dependency.
	 */
	trait LazyLogServiceTrait {
		/**
		 * @var ILogService
		 */
		protected $logService;

		/**
		 * @param ILogService $logService
		 */
		public function lazyLogService(ILogService $logService) {
			$this->logService = $logService;
		}
	}
