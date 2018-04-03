<?php
	declare(strict_types=1);
	namespace Edde\Service\Config;

	use Edde\Config\IConfigService;

	trait ConfigService {
		/** @var IConfigService */
		protected $configService;

		/**
		 * @param IConfigService $configService
		 */
		public function injectConfigService(IConfigService $configService): void {
			$this->configService = $configService;
		}
	}
