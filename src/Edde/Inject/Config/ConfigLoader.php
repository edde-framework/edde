<?php
	declare(strict_types=1);
	namespace Edde\Inject\Config;

	use Edde\Config\IConfigLoader;

	trait ConfigLoader {
		/** @var \Edde\Config\IConfigLoader */
		protected $configLoader;

		/**
		 * @param \Edde\Config\IConfigLoader $configLoader
		 */
		public function lazyConfigLoader(\Edde\Config\IConfigLoader $configLoader): void {
			$this->configLoader = $configLoader;
		}
	}
