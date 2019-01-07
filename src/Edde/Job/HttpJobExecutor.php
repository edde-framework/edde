<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Message\IPacket;
	use Edde\Service\Config\ConfigService;
	use function json_encode;

	class HttpJobExecutor extends Edde implements IJobExecutor {
		use ConfigService;
		protected $url;

		/** @inheritdoc */
		public function execute(IPacket $packet): IJobExecutor {
			file_get_contents($this->url, false, stream_context_create([
				'http' => [
					'header'  => "Content-type: application/json\r\n",
					'method'  => 'POST',
					'content' => json_encode($packet->export()),
				],
			]));
			return $this;
		}

		protected function handleInit(): void {
			parent::handleInit();
			$section = $this->configService->optional('http-job-executor');
			$this->url = $section->optional('url', 'http://localhost/rest/message.bus');
		}
	}
