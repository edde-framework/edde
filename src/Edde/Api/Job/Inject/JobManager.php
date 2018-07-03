<?php
	declare(strict_types=1);

	namespace Edde\Api\Job\Inject;

	use Edde\Api\Job\IJobManager;

	trait JobManager {
		/**
		 * @var IJobManager
		 */
		protected $jobManager;

		/**
		 * @param IJobManager $jobManager
		 */
		public function lazyJobManager(IJobManager $jobManager) {
			$this->jobManager = $jobManager;
		}
	}
