<?php
	declare(strict_types=1);
	namespace Edde\Cli\Job;

	use Edde\Controller\CliController;
	use Edde\Service\Job\JobManager;
	use Edde\Service\Job\JobQueue;
	use Edde\Service\Log\LogService;

	class ManagerController extends CliController {
		use LogService;
		use JobQueue;
		use JobManager;
		protected $running = true;

		public function actionRun(): void {
			$this->printf('Job: Running endless loop');
			$this->jobManager->run();
			$this->printf('Job: Finished');
		}

		public function actionJob(): void {
			$this->printf('Executing job [%s]', $uuid = $this->getParams()['job']);
			$this->jobQueue->execute($uuid);
			$this->printf('Done job [%s]', $uuid);
		}
	}
