<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Service\Config\ConfigService;
	use Edde\Service\Job\JobQueue;
	use Edde\Service\Storage\Storage;
	use function parse_url;
	use function sprintf;

	class HttpJobExecutor extends Edde implements IJobExecutor {
		use Storage;
		use JobQueue;
		use ConfigService;
		protected $url;
		/** @var array */
		protected $parts;

		/** @inheritdoc */
		public function execute(string $job): IJobExecutor {
			$this->storage->transaction(function () use ($job) {
				$this->jobQueue->state($job, JobSchema::STATE_SCHEDULED);
				if (($socket = fsockopen($this->parts['host'], isset($this->parts['port']) ? $this->parts['port'] : 80, $status, $error, 15)) === false) {
					throw new JobException(sprintf('Cannot connect to [%s].', $this->url));
				}
				fwrite($socket, vsprintf("GET %s HTTP/1.1\r\nHost: %s\r\nContent-Type: application/json\r\nContent-Length: %d\r\nConnection: Close\r\n\r\n", [
					sprintf($this->parts['path'], $job),
					$this->parts['host'],
				]));
				fclose($socket);
			});
			return $this;
		}

		protected function handleInit(): void {
			parent::handleInit();
			$section = $this->configService->optional('http-job-executor');
			$this->parts = parse_url($this->url = $section->optional('url', 'http://localhost/rest/job.manager/execute?job=%s'));
		}
	}
