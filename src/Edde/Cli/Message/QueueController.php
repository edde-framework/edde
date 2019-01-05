<?php
	declare(strict_types=1);
	namespace Edde\Cli\Message;

	use DateTime;
	use Edde\Controller\CliController;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Job\JobQueue;
	use Exception;

	class QueueController extends CliController {
		use ConfigService;
		use JobQueue;

		/**
		 * this MUST be called synchronously (enqueues messages for "execute");
		 * execute without this phase should do nothing
		 *
		 * @throws Exception
		 */
		public function actionEnqueue() {
			printf("[%s] Enqueue: Sleeping for [%ds]!\n",
				(new DateTime())->format('Y-m-d H:i:s'),
				$sleep = $this->configService->optional('job')->optional('sleep', 30)
			);
			sleep($sleep);
			printf("[%s] Enqueue: Enqueuing messages\n", (new DateTime())->format('Y-m-d H:i:s'));
			$this->jobQueue->push();
			printf("[%s] Enqueue: Done!\n", (new DateTime())->format('Y-m-d H:i:s'));
		}

		/**
		 * actually executes enqueued messages
		 *
		 * @throws Exception
		 */
		public function actionExecute() {
			printf("[%s] Execute: Executing Message Queue\n", (new DateTime())->format('Y-m-d H:i:s'));
			$this->jobQueue->execute();
			printf("[%s] Execute: Done!\n", (new DateTime())->format('Y-m-d H:i:s'));
		}
	}
