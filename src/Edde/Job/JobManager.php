<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Job\JobQueue;
	use Edde\Storage\EmptyEntityException;

	class JobManager extends Edde implements IJobManager {
		use JobQueue;
		use ConfigService;
		protected $running;

		public function __construct() {
			$this->running = true;
		}

		/** @inheritdoc */
		public function run(): IJobManager {
			$config = $this->configService->require('jobs');
			$binary = $config->require('binary');
			/**
			 * which controller should pickup the job
			 */
			$params = [$config->optional('controller', 'job.manager/job')];
			/**
			 * how many concurrent jobs could be run
			 */
			$limit = $config->optional('limit', 8);
			/**
			 * heartbeat rate
			 */
			$rate = $config->optional('rate', 250);
			$param = $config->optional('param', 'job');
			pcntl_async_signals(true);
			pcntl_signal(SIGTERM, [$this, 'handleShutdown']);
			pcntl_signal(SIGINT, [$this, 'handleShutdown']);
			$pids = [];
			while ($this->running) {
				try {
					$job = $this->jobQueue->enqueue();
					/**
					 * limit concurrency level
					 */
					if (count($pids) < $limit) {
						$params[] = '--' . $param . '=' . $job['uuid'];
						/**
						 * fork and replace fork by a new binary
						 */
						($pids[] = pcntl_fork()) === 0 && pcntl_exec($binary, $params);
					}
				} catch (EmptyEntityException $exception) {
					/**
					 * noop
					 */
				}
				/**
				 * pickup children process to prevent zombies
				 */
				pcntl_waitpid(0, $status, WNOHANG);
				/**
				 * remove already executed PIDs
				 */
				foreach ($pids as $i => $pid) {
					if (posix_getpgid($pid) === false) {
						unset($pids[$i]);
					}
				}
				/**
				 * heartbeat rate to keep stuff on rails
				 */
				usleep($rate * 1000);
			}
			/**
			 * wait until all children processes are done
			 */
			while (pcntl_waitpid(0, $status) !== -1) {
				;
			}
			return $this;
		}

		public function handleShutdown() {
			$this->running = false;
		}
	}
