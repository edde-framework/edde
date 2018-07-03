<?php
	declare(strict_types=1);

	namespace Edde\Api\Job\Inject;

	use Edde\Api\Job\IQueueList;

	trait QueueList {
		/**
		 * @var IQueueList
		 */
		protected $queueList;

		/**
		 * @param IQueueList $queueList
		 */
		public function lazyQueueList(IQueueList $queueList) {
			$this->queueList = $queueList;
		}
	}
