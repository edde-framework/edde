<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Service\Job\JobExecutor;
	use Edde\Service\Job\JobQueue;

	class JobManager extends Edde implements IJobManager {
		use JobQueue;
		use JobExecutor;

		/** @inheritdoc */
		public function run(): IJobManager {
			while (true) {
				/**
				 * because job manager should be managed by some service (systemd, runit, ...) it could
				 * die hard on any exception as it will be re-executed again
				 */
				try {
					$this->tick();
				} catch (HolidayException $exception) {
					/**
					 * noop
					 */
				}
				/**
				 * heartbeat rate to keep stuff on rails
				 */
				usleep(250 * 1000);
			}
			return $this;
		}

		/** @inheritdoc */
		public function tick(): IJobManager {
			$this->jobExecutor->execute($this->jobQueue->pick()['uuid']);
			return $this;
		}
	}
