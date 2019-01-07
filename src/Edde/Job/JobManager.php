<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Service\Job\JobQueue;
	use Edde\Storage\EmptyEntityException;
	use function sprintf;

	class JobManager extends Edde implements IJobManager {
		use JobQueue;

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
					if (($pid = $this->pids[] = pcntl_fork()) === 0) {
						if (pcntl_exec($this->binary, [
								$this->controller,
								'--' . $this->param . '=' . $job['uuid'],
							]) === false) {
							throw new JobException(sprintf('Cannot execute a job [%s].', $job['uuid']));
						}
					} else if ($pid === -1) {
						throw new JobException(sprintf('Cannot fork a job [%s].', $job['uuid']));
					}
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
	}
