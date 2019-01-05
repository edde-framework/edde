<?php
	declare(strict_types=1);
	namespace Edde\Cli\Job;

	use Edde\Controller\CliController;
	use Edde\Service\Log\LogService;
	use Edde\Service\Security\RandomService;
	use function pcntl_async_signals;
	use function pcntl_exec;
	use function pcntl_fork;
	use function pcntl_signal;
	use function sleep;
	use const SIGINT;
	use const SIGTERM;
	use const WNOHANG;

	class ManagerController extends CliController {
		use LogService;
		use RandomService;
		protected $running = true;

		public function actionRun(): void {
			$this->printf('Job: Running endless loop');
			$this->installHandlers();
			$this->run();
			$this->printf('Job: Finished');
		}

		public function actionJob(): void {
			$this->printf('Executing job [%s]', $this->getParams()['job']);
			sleep(2);
			$this->printf('Done job [%s]', $this->getParams()['job']);
		}

		public function handleShutdown() {
			$this->running = false;
			while (pcntl_waitpid(0, $status) !== -1) {
				;
			}
		}

		protected function installHandlers() {
			pcntl_async_signals(true);
			pcntl_signal(SIGTERM, [$this, 'handleShutdown']);
			pcntl_signal(SIGINT, [$this, 'handleShutdown']);
		}

		protected function run() {
			for ($i = 0; $i < 5; $i++) {
				if (pcntl_fork() === 0) {
					pcntl_exec($GLOBALS['argv'][0], ['job.manager/job', '--job=' . $this->randomService->uuid()]);
				}
				pcntl_waitpid(0, $status, WNOHANG);
			}
		}
	}
