<?php
	declare(strict_types=1);
	namespace Edde\Cli\Job;

	use Edde\Controller\CliController;
	use Edde\Service\Job\JobQueue;
	use Edde\Service\Log\LogService;
	use Edde\Service\Security\RandomService;
	use function pcntl_async_signals;
	use function pcntl_exec;
	use function pcntl_fork;
	use function pcntl_signal;
	use function posix_getpgid;
	use function usleep;
	use const SIGINT;
	use const SIGTERM;
	use const WNOHANG;

	class ManagerController extends CliController {
		use LogService;
		use RandomService;
		use JobQueue;
		protected $running = true;

		public function actionRun(): void {
			$this->printf('Job: Running endless loop');
			$this->installHandlers();
			$this->run();
			$this->wait();
			$this->printf('Job: Finished');
		}

		public function actionJob(): void {
			$this->printf('Executing job [%s]', $uuid = $this->getParams()['job']);
			$this->jobQueue->execute($uuid);
			$this->printf('Done job [%s]', $uuid);
		}

		public function handleShutdown() {
			$this->running = false;
			$this->wait();
		}

		public function wait() {
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
			$binary = $GLOBALS['argv'][0];
			/**
			 * which controller should pickup the job
			 */
			$params = ['job.manager/job'];
			/**
			 * how many concurrent jobs could be run
			 */
			$concurrency = 4;
			/**
			 * heartbeat rate
			 */
			$rate = 250;
			$pids = [];
			while ($this->running) {
				/**
				 * heartbeat rate to keep stuff on rails
				 */
				usleep($rate * 1000);
				$this->printf('workers: %d/%d', count($pids), $concurrency);
				/**
				 * limit concurrency level
				 */
				if (count($pids) < $concurrency) {
					$params[] = '--job=' . $this->randomService->uuid();
					/**
					 * fork and replace fork by a new binary
					 */
					($pids[] = pcntl_fork()) === 0 && pcntl_exec($binary, $params);
				}
				/**
				 * pickup children process to prevent zombies
				 */
				pcntl_waitpid(0, $status, WNOHANG);
				foreach ($pids as $i => $pid) {
					if (posix_getpgid($pid) === false) {
						unset($pids[$i]);
					}
				}
			}
		}
	}
