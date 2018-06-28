<?php
	declare(strict_types=1);
	namespace Edde\Configurable;

	use Edde\Edde;

	abstract class AbstractConfigurator extends Edde implements IConfigurator {
		/** @inheritdoc */
		public function configure($instance) {
		}
	}
