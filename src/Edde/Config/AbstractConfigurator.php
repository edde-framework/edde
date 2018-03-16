<?php
	declare(strict_types=1);
	namespace Edde\Config;

	use Edde\Object;

	abstract class AbstractConfigurator extends Object implements IConfigurator {
		/** @inheritdoc */
		public function configure($instance) {
		}
	}
