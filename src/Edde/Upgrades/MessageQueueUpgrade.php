<?php
	declare(strict_types=1);
	namespace Edde\Upgrades;

	use Edde\Message\BatchSchema;
	use Edde\Message\MessageQueueSchema;
	use Edde\Upgrade\AbstractUpgrade;

	class MessageQueueUpgrade extends AbstractUpgrade {
		/** @inheritdoc */
		public function getVersion(): string {
			return 'message-queue';
		}

		/** @inheritdoc */
		public function upgrade(): void {
			$this->storage->creates([
				BatchSchema::class,
				MessageQueueSchema::class,
			]);
		}
	}
