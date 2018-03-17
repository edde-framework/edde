<?php
	declare(strict_types=1);
	namespace Edde\Inject\Config;

	use Edde\Config\IConfigService;

	trait ConfigService {
		/** @var IConfigService */
		protected $configService;

		/**
		 * @param IConfigService $configService
		 */
		public function lazyConfigService(IConfigService $configService): void {
			$this->configService = $configService;
		}
	}
