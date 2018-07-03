<?php
	declare(strict_types=1);

	namespace Edde\Api\Job\Inject;

	use Edde\Api\Job\IJobQueue;

	trait JobQueue {
		/**
		 * @var IJobQueue
		 */
		protected $jobQueue;

		/**
		 * @param IJobQueue $jobQueue
		 */
		public function lazyJobQueue(IJobQueue $jobQueue) {
			$this->jobQueue = $jobQueue;
		}
	}
