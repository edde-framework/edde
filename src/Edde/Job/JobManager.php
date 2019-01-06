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
		protected $pids;
		protected $binary;
		protected $controller;
		protected $limit;
		protected $rate;
		protected $param;

		public function __construct() {
			$this->running = true;
			$this->pids = [];
		}

		public function startup(): IJobManager {
			pcntl_async_signals(true);
			pcntl_signal(SIGTERM, [$this, 'handleShutdown']);
			pcntl_signal(SIGINT, [$this, 'handleShutdown']);
			return $this;
		}

		/** @inheritdoc */
		public function run(): IJobManager {
			$this->startup();
			while ($this->running) {
				$this->tick();
				/**
				 * heartbeat rate to keep stuff on rails
				 */
				usleep($this->rate * 1000);
			}
			$this->shutdown();
			return $this;
		}

		/** @inheritdoc */
		public function tick(): IJobManager {
			try {
				$job = $this->jobQueue->enqueue();
				/**
				 * limit concurrency level
				 */
				if (count($this->pids) < $this->limit) {
					/**
					 * fork and replace fork by a new binary
					 */
					($this->pids[] = pcntl_fork()) === 0 && pcntl_exec($this->binary, [
						$this->controller,
						'--' . $this->param . '=' . $job['uuid'],
					]);
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
			foreach ($this->pids as $i => $pid) {
				if (posix_getpgid($pid) === false) {
					unset($this->pids[$i]);
				}
			}
			return $this;
		}

		public function shutdown(): IJobManager {
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

		protected function handleInit(): void {
			$config = $this->configService->require('jobs');
			$this->binary = $config->optional('binary', './cli');
			/**
			 * which controller should pickup the job
			 */
			$this->controller = $config->optional('controller', 'job.manager/execute');
			/**
			 * how many concurrent jobs could be run
			 */
			$this->limit = $config->optional('limit', 8);
			/**
			 * heartbeat rate
			 */
			$this->rate = $config->optional('rate', 250);
			$this->param = $config->optional('param', 'job');
		}
	}
