<?php
	declare(strict_types=1);
	namespace Edde\Job;

	use Edde\Message\Message;
	use Edde\Service\Job\JobManager;
	use Edde\Service\Job\JobQueue;
	use Edde\Service\Schema\SchemaManager;
	use Edde\Service\Storage\Storage;
	use Edde\Service\Upgrade\UpgradeManager;
	use Edde\Storage\EmptyEntityException;
	use Edde\TestCase;
	use Edde\Upgrade\CurrentVersionException;
	use Edde\Upgrade\IUpgradeManager;
	use Edde\Upgrade\UpgradeManagerConfigurator;
	use Edde\Upgrade\UpgradeSchema;
	use function chdir;
	use function sleep;

	class JobFlowTest extends TestCase {
		use JobQueue;
		use JobManager;
		use SchemaManager;
		use UpgradeManager;
		use Storage;

		/**
		 * @throws EmptyEntityException
		 */
		public function testJobFlow() {
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

		/**
		 * @depends testJobFlow
		 */
		public function testAsyncJobs() {
			$job = $this->jobQueue->push(new Message('async', 'edde.message.common-message-service', ['foo' => 'bar']));
			$this->jobManager->startup();
			$this->jobManager->tick();
			$this->jobManager->shutdown();
			sleep(2);
			$job = $this->jobQueue->byUuid($job['uuid']);
			self::assertSame(JobSchema::STATE_DONE, $job['state']);
		}

		public function setUp() {
			parent::setUp();
			chdir('/edde');
			$this->container->registerConfigurator(IUpgradeManager::class, $this->container->create(UpgradeManagerConfigurator::class));
			try {
				$this->upgradeManager->upgrade();
			} catch (CurrentVersionException $exception) {
			}
		}

		public function tearDown() {
			parent::tearDown();
			$this->storage->exec('DROP TABLE s:schema', [
				'$query' => [
					's' => JobSchema::class,
				],
			]);
			$this->storage->exec('DROP TABLE s:schema', [
				'$query' => [
					's' => UpgradeSchema::class,
				],
			]);
		}
	}
