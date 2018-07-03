<?php
	declare(strict_types=1);

	namespace Edde\Api\Job;

	trait LazyJobManagerTrait {
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
