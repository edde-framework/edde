<?php
	declare(strict_types=1);
	namespace Edde\Pub\Cli\Job;

	use Edde\Controller\CliController;
	use Edde\Service\Job\JobManager;
	use Edde\Service\Job\JobQueue;

	class ManagerController extends CliController {
		use JobQueue;
		use JobManager;

		public function actionRun(): void {
			$this->printf('Job: Running endless loop');
			$this->jobManager->run();
			$this->printf('Job: Finished');
		}

		public function actionReset(): void {
			$this->printf('Resetting dead jobs');
			$this->jobQueue->reset();
			$this->printf('Done');
		}

		public function actionCleanup(): void {
			$this->printf('Cleaning up');
			$this->jobQueue->cleanup();
			$this->printf('Done');
		}
	}
