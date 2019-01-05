<?php
	declare(strict_types=1);
	namespace Edde\Service\Job;

	use Edde\Job\IJobQueue;

	trait JobQueue {
		/** @var IJobQueue */
		protected $jobQueue;

		/**
		 * @param IJobQueue $jobQueue
		 */
		public function injectJobQueue(IJobQueue $jobQueue): void {
			$this->jobQueue = $jobQueue;
		}
	}
