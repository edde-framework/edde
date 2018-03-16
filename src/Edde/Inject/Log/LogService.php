<?php
	declare(strict_types=1);
	namespace Edde\Inject\Log;

	use Edde\Log\ILogService;

	/**
	 * Lazy log service dependency.
	 */
	trait LogService {
		/** @var \Edde\Log\ILogService */
		protected $logService;

		/**
		 * @param \Edde\Log\ILogService $logService
		 */
		public function lazyLogService(\Edde\Log\ILogService $logService) {
			$this->logService = $logService;
		}
	}
