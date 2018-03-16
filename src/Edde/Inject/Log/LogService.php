<?php
	declare(strict_types=1);
	namespace Edde\Inject\Log;

	use Edde\Api\Log\ILogService;

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
