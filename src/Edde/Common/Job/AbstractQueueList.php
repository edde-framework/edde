<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\IJobQueue;
	use Edde\Api\Job\IQueueList;
	use Edde\Api\Job\JobQueueException;
	use Edde\Api\Protocol\IElement;

	abstract class AbstractQueueList extends AbstractJobQueue implements IQueueList {
		/**
		 * @var IJobQueue[]
		 */
		protected $jobQueueList = [];

		/**
		 * @inheritdoc
		 */
		public function addJobQueue(IJobQueue $jobQueue): IQueueList {
			$this->jobQueueList[] = $jobQueue;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element): IJobQueue {
			throw new JobQueueException(sprintf('Cannot queue job [%s] to [%s]! Use concrete queue [%s] instead of general queue list.', $element->getType(), IQueueList::class, IJobQueue::class));
		}

		/**
		 * @inheritdoc
		 */
		public function hasJob(): bool {
			foreach ($this->jobQueueList as $jobQueue) {
				if ($jobQueue->hasJob()) {
					return true;
				}
			}
			return false;
		}
	}
