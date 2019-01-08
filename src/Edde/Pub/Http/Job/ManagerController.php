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
	use function microtime;

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
			$time = microtime(true);
			$job = $this->jobQueue->state($this->getParams()['job'], JobSchema::STATE_RUNNING);
			$state = JobSchema::STATE_SUCCESS;
			$result = null;
			try {
				$this->messageBus->execute(
					$this->messageBus->importMessage(
						$job['message']
					)
				);
			} catch (Throwable $exception) {
				$state = JobSchema::STATE_FAILED;
				$result = $exception->getMessage();
				throw $exception;
			} finally {
				$job['runtime'] = (microtime(true) - $time) * 1000;
				$this->jobQueue->update($job);
				$this->jobQueue->state($job['uuid'], $state, $result);
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
