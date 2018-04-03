<?php
	declare(strict_types=1);
	namespace Edde\Configurator\Schema;

	use Edde\Config\AbstractConfigurator;
	use Edde\Service\Container\Container;

	class SchemaManagerConfigurator extends AbstractConfigurator {
		use Container;

		public function configure($instance) {
			parent::configure($instance);
		}
	}
