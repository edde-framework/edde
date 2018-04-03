<?php
	declare(strict_types=1);
	namespace Edde\Service\Config;

	use Edde\Config\IConfigLoader;

	trait ConfigLoader {
		/** @var IConfigLoader */
		protected $configLoader;

		/**
		 * @param IConfigLoader $configLoader
		 */
		public function injectConfigLoader(IConfigLoader $configLoader): void {
			$this->configLoader = $configLoader;
		}
	}
