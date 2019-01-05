<?php
	declare(strict_types=1);
	namespace Edde\Cli\Job;

	use Edde\Controller\CliController;
	use Edde\Service\Log\LogService;
	use Throwable;
	use function pcntl_async_signals;
	use function pcntl_signal;
	use function sleep;
	use const SIGINT;
	use const SIGTERM;
	use const WNOHANG;

	class ManagerController extends CliController {
		use LogService;
		protected $running = true;

		public function actionRun(): void {
			$this->printf('Job: Running endless loop');
			$this->installHandlers();
			$this->run();
			$this->printf('Job: Finished');
		}

		public function handleShutdown() {
			$this->running = false;
		}

		protected function installHandlers() {
			pcntl_async_signals(true);
			pcntl_signal(SIGTERM, [$this, 'handleShutdown']);
			pcntl_signal(SIGINT, [$this, 'handleShutdown']);
		}

		protected function run() {
			$count = 0;
			while ($this->running) {
//				usleep(50000);
				sleep(3);
				/**
				 * just wait a bit to not totally overload a node
				 */
				$job = ['uuid' => '213'];
				switch ($fork = pcntl_fork()) {
					/**
					 * error
					 */
					case -1:
						$this->printf('Fork failed, exiting.');
						$this->running = false;
						break;
					/**
					 * child
					 */
					case 0:
						$this->printf('Executing job [%s]', $job['uuid']);
						try {
							sleep(3);
							$this->printf('Done job [%s]', $job['uuid']);
						} catch (Throwable $exception) {
							$this->printf('Failed job [%s]', $job['uuid']);
							$this->logService->exception($exception);
						}
						break;
					/**
					 * parent
					 */
					default:
						$this->printf('waiting for pid');
						pcntl_waitpid(0, $status, WNOHANG);
				}
			}
		}
	}
