<?php
	declare(strict_types=1);
	namespace Edde\Config;

	use Edde\Obj3ct;

	abstract class AbstractConfigurator extends Obj3ct implements IConfigurator {
		/** @inheritdoc */
		public function configure($instance) {
		}
	}
