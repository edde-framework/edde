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

		public function actionPause(): void {
			$this->printf('Pausing JobManager');
			$this->jobManager->pause();
			$this->printf('Done');
		}

		public function actionUnpause(): void {
			$this->printf('Resuming JobManager');
			$this->jobManager->pause(false);
			$this->printf('Done');
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

		public function actionClear(): void {
			$this->printf('Clearing whole job queue');
			$this->jobQueue->clear();
			$this->printf('Done');
		}
	}
