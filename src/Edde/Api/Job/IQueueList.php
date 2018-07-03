<?php
	declare(strict_types=1);

	namespace Edde\Api\Job;

	interface IQueueList extends IJobQueue {
		/**
		 * add job queue to the list
		 *
		 * @param IJobQueue $jobQueue
		 *
		 * @return IQueueList
		 */
		public function addJobQueue(IJobQueue $jobQueue): IQueueList;
	}
