<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Job\JobQueue;
	use Edde\Service\Storage\Storage;
	use Edde\Url\Url;
	use function sprintf;

	class HttpJobExecutor extends Edde implements IJobExecutor {
		use Storage;
		use JobQueue;
		use ConfigService;
		protected $url;

		/** @inheritdoc */
		public function execute(string $job): IJobExecutor {
			$this->storage->transaction(function () use ($job) {
				$this->jobQueue->state($job, JobSchema::STATE_SCHEDULED);
				$url = Url::create($this->url)->setParam('job', $job);
				if (($socket = fsockopen($url->getHost(), $url->getPort(80), $status, $error, 15)) === false) {
					throw new JobException(sprintf('Cannot connect to [%s].', $this->url));
				}
				fwrite($socket, vsprintf("GET %s HTTP/1.1\r\nHost: %s\r\nConnection: Close\r\n\r\n", [
					$url->getPath(),
					$url->getHost(),
				]));
				fclose($socket);
			});
			return $this;
		}

		protected function handleInit(): void {
			parent::handleInit();
			$section = $this->configService->optional('http-job-executor');
			$this->url = $section->optional('url', 'http://localhost/job.manager/execute');
		}
	}
