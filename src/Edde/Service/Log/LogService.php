<?php
	declare(strict_types=1);
	namespace Edde\Service\Log;

	use Edde\Log\ILogService;

	/**
	 * Lazy log service dependency.
	 */
	trait LogService {
		/** @var ILogService */
		protected $logService;

		/**
		 * @param ILogService $logService
		 */
		public function lazyLogService(ILogService $logService) {
			$this->logService = $logService;
		}
	}
