<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Message\Message;
	use Edde\Service\Job\JobQueue;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Upgrade\UpgradeManager;
	use Edde\TestCase;
	use Edde\Upgrade\CurrentVersionException;
	use Edde\Upgrade\UpgradeException;
	use Edde\Upgrade\UpgradeSchema;
	use Throwable;

	class JobFlowTest extends TestCase {
		use JobQueue;
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
			$this->jobQueue->push(new Message('async', 'edde.message.common-message-service', ['foo' => 'bar']));
			$this->jobQueue->push(new Message('async', 'edde.message.common-message-service', ['bar' => 'foo']));
			$job1 = $this->jobQueue->enqueue();
			$job2 = $this->jobQueue->enqueue();
			self::assertNotEquals($job1, $job2);
			self::assertEquals((object)['foo' => 'bar'], $job1['message']->attrs);
			self::assertEquals((object)['bar' => 'foo'], $job2['message']->attrs);
		}
	}
