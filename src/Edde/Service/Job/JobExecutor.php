<?php
	declare(strict_types=1);
	namespace Edde\Service\Job;

	use Edde\Job\IJobExecutor;

	trait JobExecutor {
		/** @var IJobExecutor */
		protected $jobExecutor;

		/**
		 * @param IJobExecutor $jobExecutor
		 */
		public function injectJobExecutor(IJobExecutor $jobExecutor): void {
			$this->jobExecutor = $jobExecutor;
		}
	}
