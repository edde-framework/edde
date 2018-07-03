<?php
	declare(strict_types=1);

	namespace Edde\Api\Job;

	trait LazyQueueListTrait {
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
