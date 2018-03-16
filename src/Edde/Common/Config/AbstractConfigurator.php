<?php
	declare(strict_types=1);
	namespace Edde\Common\Config;

	use Edde\Api\Config\IConfigurator;
	use Edde\Object;

	abstract class AbstractConfigurator extends Object implements IConfigurator {
		/**
		 * @inheritdoc
		 */
		public function configure($instance) {
		}
	}
