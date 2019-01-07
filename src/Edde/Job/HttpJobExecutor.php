<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Edde;
	use Edde\Message\IPacket;
	use Edde\Service\Config\ConfigService;

	class HttpJobExecutor extends Edde implements IJobExecutor {
		use ConfigService;
		protected $url;

		/** @inheritdoc */
		public function execute(IPacket $packet): IJobExecutor {
			return $this;
		}

		protected function handleInit(): void {
			parent::handleInit();
			$section = $this->configService->optional('http-job-executor');
			$this->url = $section->optional('url', 'http://localhost/rest/message.bus');
		}
	}
