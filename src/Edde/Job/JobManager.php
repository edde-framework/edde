<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Service\Job\JobQueue;

	class JobManager extends Edde implements IJobManager {
		use JobQueue;

		/** @inheritdoc */
		public function run(): IJobManager {
			while (true) {
				$this->tick();
				/**
				 * heartbeat rate to keep stuff on rails
				 */
				usleep(750 * 1000);
			}
			return $this;
		}

		/** @inheritdoc */
		public function tick(): IJobManager {
			return $this;
		}
	}
