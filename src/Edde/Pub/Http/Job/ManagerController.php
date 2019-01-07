<?php
	declare(strict_types=1);
	namespace Edde\Pub\Http\Job;

	use Edde\Application\RouterException;
	use Edde\Controller\RestController;
	use Edde\Job\JobSchema;
	use Edde\Runtime\RuntimeException;
	use Edde\Service\Job\JobQueue;
	use Edde\Service\Message\MessageBus;
	use Edde\Url\UrlException;
	use Throwable;
	use function implode;

	class ManagerController extends RestController {
		use JobQueue;
		use MessageBus;

		/**
		 * @throws Throwable
		 * @throws RouterException
		 * @throws RuntimeException
		 * @throws UrlException
		 */
		public function actionExecute(): void {
			$this->jobQueue->state($job = $this->getParams()['job'], JobSchema::STATE_RUNNING);
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
			$this->textResponse(sprintf('job done [%s]', $job))->execute();
		}

		public function actionCleanup(): void {
			$this->jobQueue->cleanup();
			$this->textResponse('ok')->execute();
		}

		public function actionStats(): void {
			$stats = [];
			foreach ($this->jobQueue->stats() as $name => $stat) {
				$stats[] = $name . ' = ' . $stat;
			}
			$this->textResponse(implode("\n", $stats))->execute();
		}
	}
