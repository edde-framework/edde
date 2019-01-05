<?php
	declare(strict_types=1);
	namespace Edde\Message;

	use Edde\Job\JobSchema;
	use Edde\Service\Message\MessageQueue;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Upgrade\UpgradeManager;
	use Edde\TestCase;
	use Edde\Upgrade\CurrentVersionException;
	use Edde\Upgrade\UpgradeException;
	use Edde\Upgrade\UpgradeSchema;
	use Throwable;

	class MessageQueueFlowTest extends TestCase {
		use MessageQueue;
		use SchemaManager;
		use UpgradeManager;
		use Storage;

		/**
		 * @throws UpgradeException
		 */
		public function testMessageQueueFlow() {
			$drops = [
				JobSchema::class,
				UpgradeSchema::class,
			];
			foreach ($drops as $drop) {
				try {
					$this->storage->exec(
						$this->storage->query('DROP TABLE t:schema', [
							't' => $drop,
						])
					);
				} catch (Throwable $exception) {
				}
			}
			try {
				$this->upgradeManager->upgrade();
			} catch (CurrentVersionException $exception) {
			}
			$this->messageQueue->enqueue(new Message('async', 'edde.message.common-message-service'));
		}
	}
