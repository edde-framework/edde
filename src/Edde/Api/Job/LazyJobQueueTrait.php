<?php
	declare(strict_types=1);

	namespace Edde\Api\Job;

	trait LazyJobQueueTrait {
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
