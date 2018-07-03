<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\LazyJobManagerTrait;
	use Edde\Api\Job\LazyJobQueueTrait;
	use Edde\Api\Protocol\Event\LazyEventBusTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyElementStoreTrait;
	use Edde\Api\Store\LazyStoreTrait;
	use Edde\Common\Protocol\Event\Event;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;

	class JobQueueTest extends TestCase {
		use LazyJobQueueTrait;
		use LazyJobManagerTrait;
		use LazyEventBusTrait;
		use LazyElementStoreTrait;
		use LazyStoreTrait;

		public function testJobQueue() {
			$this->store->drop();
			self::assertFalse($this->jobQueue->hasJob(), 'job queue still has jobs');
			$this->jobQueue->queue($event = new Event('some-event'));
			$this->jobQueue->queue(new Event('some-event'));
			$this->jobQueue->queue(new Event('some-event2'));
			$this->jobQueue->queue(new Event('some-event3'));
			$this->jobManager->queue(new Event('some-event4'));
			self::assertTrue($this->jobQueue->hasJob(), 'job queue is empty but there should be some jobs');
			$eventList = [
				'some-event'  => 0,
				'some-event2' => 0,
				'some-event3' => 0,
				'some-event4' => 0,
			];
			$this->eventBus->listen('some-event', function (IElement $element) use (&$eventList) {
				$eventList[$element->getAttribute('event')]++;
			});
			$this->eventBus->listen('some-event2', function (IElement $element) use (&$eventList) {
				$eventList[$element->getAttribute('event')]++;
			});
			$this->eventBus->listen('some-event3', function (IElement $element) use (&$eventList) {
				$eventList[$element->getAttribute('event')]++;
			});
			$this->eventBus->listen('some-event4', function (IElement $element) use (&$eventList) {
				$eventList[$element->getAttribute('event')]++;
			});
			$this->jobManager->execute();
			self::assertEquals($event, $this->elementStore->load($event->getId()));
			self::assertFalse($this->jobManager->hasJob(), 'job queue still has jobs');
			self::assertEquals([
				'some-event'  => 2,
				'some-event2' => 1,
				'some-event3' => 1,
				'some-event4' => 1,
			], $eventList);
		}

		protected function setUp() {
			ContainerFactory::autowire($this);
		}
	}
