<?php
	declare(strict_types=1);
	namespace Edde\Pub\Rest\Job;

	use Edde\Controller\RestController;
	use Edde\Job\JobSchema;
	use Edde\Service\Job\JobQueue;
	use Edde\Service\Message\MessageBus;
	use Throwable;

	class ManagerController extends RestController {
		use JobQueue;
		use MessageBus;

		public function actionExecute(): void {
			$this->jobQueue->state($job = $this->getParams()['job'], JobSchema::STATE_STARTED);
			try {
				$this->messageBus->packet(
					$this->messageBus->importPacket(
						$this->jobQueue->byUuid($job)['packet']
					)
				);
				$this->jobQueue->state($job, JobSchema::STATE_SUCCESS);
			} catch (Throwable $exception) {
				$this->jobQueue->state($job, JobSchema::STATE_FAILED);
				throw $exception;
			}
		}

		public function actionCleanup(): void {
			$this->jobQueue->cleanup();
			$this->jsonResponse('ok')->execute();
		}
	}
