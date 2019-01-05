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
						$this->storage->query('DROP TABLE s:schema', [
							's' => $drop,
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
			self::assertEquals(JobSchema::STATE_ENQUEUED, $job1['state']);
			self::assertEquals(JobSchema::STATE_ENQUEUED, $job2['state']);
			$packet1 = $this->jobQueue->execute($job1['uuid']);
			$packet2 = $this->jobQueue->execute($job2['uuid']);
			$job1 = $this->jobQueue->byUuid($job1['uuid']);
			$job2 = $this->jobQueue->byUuid($job2['uuid']);
			self::assertEquals(JobSchema::STATE_DONE, $job1['state']);
			self::assertEquals(JobSchema::STATE_DONE, $job2['state']);
			self::assertCount(1, $packet1->messages());
			self::assertCount(1, $packet2->messages());
			[$message1] = $packet1->messages();
			[$message2] = $packet2->messages();
			self::assertSame(['foo' => 'bar'], $message1->getAttrs());
			self::assertSame('mwah', $message1->getType());
			self::assertSame(['bar' => 'foo'], $message2->getAttrs());
			self::assertSame('mwah', $message2->getType());
		}
	}
