<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Message\IPacket;
	use Edde\Service\Config\ConfigService;
	use function fwrite;
	use function json_encode;
	use function parse_url;
	use function strlen;
	use function vsprintf;

	class HttpJobExecutor extends Edde implements IJobExecutor {
		use ConfigService;
		protected $url;
		/** @var array */
		protected $parts;

		/** @inheritdoc */
		public function execute(IPacket $packet): IJobExecutor {
			$content = json_encode($packet->export());
			if (($socket = fsockopen($this->parts['host'], isset($this->parts['port']) ? $this->parts['port'] : 80, $status, $error, 15)) === false) {
				throw new JobException(sprintf('Cannot connect to [%s].', $this->url));
			}
			fwrite($socket, vsprintf("POST %s HTTP/1.1\r\nHost: %s\r\nContent-Type: application/json\r\nContent-Length: %d\r\nConnection: Close\r\n\r\n", [
				$this->parts['path'],
				$this->parts['host'],
				strlen($content),
			]));
			fwrite($socket, $content);
			fclose($socket);
			return $this;
		}

		protected function handleInit(): void {
			parent::handleInit();
			$section = $this->configService->optional('http-job-executor');
			$this->parts = parse_url($this->url = $section->optional('url', 'http://localhost/rest/message.bus'));
		}
	}
