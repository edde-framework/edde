<?php
	declare(strict_types=1);
	namespace Edde\Cli\Job;

	use Edde\Controller\CliController;
	use function pcntl_async_signals;
	use function pcntl_signal;
	use const SIGINT;
	use const SIGTERM;

	class ManagerController extends CliController {
		protected $running = true;

		public function actionRun(): void {
			$this->printf('Job: Running endless loop');
			pcntl_async_signals(true);
			pcntl_signal(SIGTERM, [$this, 'handleShutdown']);
			pcntl_signal(SIGINT, [$this, 'handleShutdown']);
			while ($this->running) {
			}
			$this->printf('Job: Finished');
		}

		public function handleShutdown() {
			$this->running = false;
		}
	}
