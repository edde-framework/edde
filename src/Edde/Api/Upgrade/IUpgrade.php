<?php
	namespace Edde\Api\Upgrade;

		use Edde\Api\Config\IConfigurable;

		interface IUpgrade extends IConfigurable {
			/**
			 * return a version of this upgrade; it could be arbitrary string as it
			 * it's not used for ordering (should not be used)
			 *
			 * @return string
			 */
			public function getVersion(): string;
		}
