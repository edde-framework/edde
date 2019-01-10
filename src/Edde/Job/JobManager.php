<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Job\JobExecutor;
	use Edde\Service\Job\JobQueue;
	use Edde\Service\Storage\Storage;
	use Edde\Storage\IEntity;

	class JobManager extends Edde implements IJobManager {
		use Storage;
		use JobQueue;
		use JobExecutor;
		use ConfigService;
		protected $limit;
		protected $rate;
		protected $sleep;

		/** @inheritdoc */
		public function run(): IJobManager {
			/**
			 * it's expected that job manager runs as a deamon, thus
			 * it's necessary to reset pending jobs
			 */
			$this->jobQueue->reset();
			/**
			 * execute never-ending story here; because job manager is run through system process manager,
			 * it doesn't matter if it fails, because it will be REBORN!
			 */
			while (true) {
				if ($this->isPaused() || $this->jobQueue->countLimit() >= $this->limit) {
					/**
					 * sleep a bit more if limit is reached
					 */
					usleep($this->sleep * 1000);
					continue;
				}
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
				usleep($this->rate * 1000);
			}
			return $this;
		}

		/** @inheritdoc */
		public function tick(): IJobManager {
			$this->jobExecutor->execute($this->jobQueue->pick()['uuid']);
			return $this;
		}

		/** @inheritdoc */
		public function isPaused(): bool {
			return $this->getEntity()['paused'] === true;
		}

		/** @inheritdoc */
		public function pause(bool $pause = true): IJobManager {
			$this->update(['paused' => $pause]);
			return $this;
		}

		protected function update(array $update): void {
			$jobManager = $this->getEntity();
			$jobManager->put($update);
			$this->storage->update($jobManager);
		}

		protected function getEntity(): IEntity {
			return $this->storage->single(JobManagerSchema::class, 'SELECT * FROM s:schema LIMIT 1', ['$query' => ['s' => JobManagerSchema::class]]);
		}

		protected function handleInit(): void {
			parent::handleInit();
			$section = $this->configService->optional('job-manager');
			$this->limit = $section->optional('limit', 8);
			$this->rate = $section->optional('rate', 100);
			$this->sleep = $section->optional('rate', 5000);
		}
	}
