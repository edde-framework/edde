<?php
	declare(strict_types=1);
	namespace Edde\Schema;

	use Edde\Configurable\AbstractConfigurator;
	use Edde\Message\MessageQueueSchema;
	use Edde\Upgrade\UpgradeSchema;

	class SchemaManagerConfigurator extends AbstractConfigurator {
		/**
		 * @param $instance ISchemaManager
		 *
		 * @throws SchemaException
		 */
		public function configure($instance) {
			parent::configure($instance);
			$instance->loads([
				UpgradeSchema::class,
				MessageQueueSchema::class,
			]);
		}
	}
