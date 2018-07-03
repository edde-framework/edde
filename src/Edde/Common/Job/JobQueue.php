<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\IJobQueue;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\Inject\ElementStore;
	use Edde\Api\Store\Inject\Store;

	class JobQueue extends AbstractJobQueue {
		use ElementStore;
		use Store;

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element): IJobQueue {
			$this->elementStore->save($element);
			$this->store->append(static::class, $element->getId());
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasJob(): bool {
			return $this->store->has(static::class);
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue() {
			foreach ($this->store->pickup(static::class, []) as $elementId) {
				yield $this->elementStore->load($elementId);
			}
		}
	}
