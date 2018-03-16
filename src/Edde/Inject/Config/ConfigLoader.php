<?php
	declare(strict_types=1);
	namespace Edde\Inject\Config;

	use Edde\Api\Config\IConfigLoader;

	trait ConfigLoader {
		/** @var IConfigLoader */
		protected $configLoader;

		/**
		 * @param IConfigLoader $configLoader
		 */
		public function lazyConfigLoader(IConfigLoader $configLoader): void {
			$this->configLoader = $configLoader;
		}
	}
