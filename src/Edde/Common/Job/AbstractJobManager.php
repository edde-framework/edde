<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\IJobManager;
	use Edde\Api\Job\IJobQueue;
	use Edde\Api\Job\Inject\JobQueue;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\Inject\ProtocolService;

	abstract class AbstractJobManager extends AbstractJobQueue implements IJobManager {
		use JobQueue;
		use ProtocolService;

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element): IJobQueue {
			$this->jobQueue->queue($element);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function queueList($elementList): IJobQueue {
			$this->jobQueue->queueList($elementList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasJob(): bool {
			return $this->jobQueue->hasJob();
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue() {
			return $this->jobQueue->dequeue();
		}

		/**
		 * @inheritdoc
		 */
		public function execute(): IJobManager {
			$jobQueue = $this->jobQueue;
			foreach ($jobQueue->dequeue() as $element) {
				$this->protocolService->execute($element);
			}
			return $this;
		}
	}
